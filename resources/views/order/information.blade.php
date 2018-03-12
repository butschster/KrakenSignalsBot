<br />
Order: {{ $order->txid }}

<span class="badge @if($order->isClosed()) badge-success @elseif($order->needAttention()) badge-warning @else badge-info @endif">
    {{ $order->status }}
</span>