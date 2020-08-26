<tr>
<td class="header">
<a href="{{ $url }}" style="display: inline-block;">
@if ($isImage)
<img src="{{$slot}}" class="logo" alt="{{$displayName}}">
@else
{{ $displayName }}
@endif
</a>
</td>
</tr>
