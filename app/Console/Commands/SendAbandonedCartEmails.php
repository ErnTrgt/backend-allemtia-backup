<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Cart;
use App\Models\User;
use App\Models\Product;
use Illuminate\Support\Facades\Mail;
use App\Mail\AbandonedCartReminder;
use Carbon\Carbon;

class SendAbandonedCartEmails extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cart:send-abandoned-emails';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sepette ürün bırakıp giden müşterilere hatırlatma e-postası gönderir';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        // 24 saat önce sepete eklenen ama hala sepette duran ürünleri bul
        $abandonedCarts = Cart::where('updated_at', '<=', Carbon::now()->subHours(24))
            ->where('updated_at', '>=', Carbon::now()->subHours(72)) // Son 3 gün içinde
            ->with(['user', 'product.user']) // Kullanıcı ve ürün sahibi bilgilerini de al
            ->get()
            ->groupBy('user_id'); // Kullanıcıya göre grupla

        $totalEmailsSent = 0;
        $emailsSentDetails = []; // Detaylı e-posta gönderim bilgileri

        $this->info("Sepette unutulan ürünler için e-posta gönderimi başlatılıyor...");
        $this->info("Toplam " . count($abandonedCarts) . " müşterinin sepetinde unutulan ürün bulundu.");

        foreach ($abandonedCarts as $userId => $cartItems) {
            $user = User::find($userId);

            // Kullanıcı yoksa veya e-posta adresi yoksa atla
            if (!$user || !$user->email) {
                $this->warn("Kullanıcı ID: {$userId} - Kullanıcı veya e-posta adresi bulunamadı, atlanıyor.");
                continue;
            }

            // Kullanıcının sepetindeki ürünleri satıcıya göre grupla
            $itemsBySeller = $cartItems->groupBy(function ($item) {
                return $item->product->user_id ?? 0;
            });

            $this->info("Kullanıcı: {$user->name} ({$user->email}) - Sepetinde {$itemsBySeller->count()} farklı satıcıdan ürün var.");

            // Her satıcı için ayrı e-posta gönder
            foreach ($itemsBySeller as $sellerId => $sellerItems) {
                // Satıcı ID'si 0 ise (ürün silinmiş veya satıcısı yoksa) atla
                if ($sellerId === 0) {
                    $this->warn("  - Satıcı ID: {$sellerId} - Geçersiz satıcı ID'si, atlanıyor.");
                    continue;
                }

                $seller = User::find($sellerId);

                // Satıcı yoksa atla
                if (!$seller) {
                    $this->warn("  - Satıcı ID: {$sellerId} - Satıcı bulunamadı, atlanıyor.");
                    continue;
                }

                // Satıcının ürünlerini hazırla
                $products = [];
                $totalValue = 0;

                foreach ($sellerItems as $item) {
                    if ($item->product) {
                        $products[] = [
                            'id' => $item->product->id,
                            'name' => $item->product->name,
                            'price' => $item->product->price,
                            'quantity' => $item->quantity,
                            'image' => $item->product->images->first()->image_path ?? null,
                            'subtotal' => $item->product->price * $item->quantity
                        ];

                        $totalValue += $item->product->price * $item->quantity;
                    }
                }

                // Ürün yoksa e-posta gönderme
                if (empty($products)) {
                    $this->warn("  - Satıcı: {$seller->name} - Ürün bulunamadı, e-posta gönderilmiyor.");
                    continue;
                }

                // E-posta verilerini hazırla
                $emailData = [
                    'user' => $user,
                    'seller' => $seller,
                    'products' => $products,
                    'totalValue' => $totalValue,
                    'cartUrl' => url('/cart')
                ];

                // E-posta gönder
                try {
                    Mail::to($user->email)->send(new AbandonedCartReminder($emailData));
                    $totalEmailsSent++;

                    // Detaylı bilgileri kaydet
                    $emailsSentDetails[] = [
                        'user_id' => $userId,
                        'user_name' => $user->name,
                        'user_email' => $user->email,
                        'seller_id' => $sellerId,
                        'seller_name' => $seller->name,
                        'product_count' => count($products),
                        'total_value' => $totalValue,
                        'products' => collect($products)->pluck('name')->toArray()
                    ];

                    $this->info("  - E-posta gönderildi: {$user->email} - Satıcı: {$seller->name} - Ürün sayısı: " . count($products) . " - Toplam değer: {$totalValue} TL");
                } catch (\Exception $e) {
                    $this->error("  - E-posta gönderilemedi: {$user->email} - Satıcı: {$seller->name} - Hata: {$e->getMessage()}");
                }
            }
        }

        // Detaylı log kaydı
        \Log::info('Sepette Unutulan Ürün Hatırlatması - Zamanlanmış Görev', [
            'total_sent' => $totalEmailsSent,
            'timestamp' => Carbon::now()->toDateTimeString(),
            'emails_sent_details' => $emailsSentDetails
        ]);

        $this->info("Toplam {$totalEmailsSent} adet sepette unutulan ürün e-postası gönderildi.");

        return Command::SUCCESS;
    }
}