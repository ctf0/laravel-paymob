<h3>Hi There</h3>
<p>here are your order details</p>
<ul>
    <li>
        something something
    </li>
</ul>

<form action="" method="POST">
    @csrf

    <p>Please select your Payment Type:</p>
    @foreach ($payment_types as $type)
        <input type="radio id="{{ $type }}" name="payment_type" value="{{ $type }}">
        <label for="{{ $type }}">{{ $type }}</label>
        <br>
    @endforeach

    <button type="submit">CheckOut</button>
</form>
