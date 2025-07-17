@props(['url'])
<tr>
<td class="header">
<a href="{{ $url }}" style="display: inline-block; text-decoration: none;">
@if (trim($slot) === 'Laravel' || trim($slot) === 'Allemtia')
<div style="text-align: center; background-color: #fff; padding: 15px 20px; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.05);">
    <span style="color: #ff6600; font-size: 36px; font-weight: 800; letter-spacing: 2px; text-transform: uppercase; font-family: Arial, Helvetica, sans-serif; text-shadow: 1px 1px 1px rgba(0,0,0,0.1);">ALLEMTIA</span>
</div>
@else
{{ $slot }}
@endif
</a>
</td>
</tr>
