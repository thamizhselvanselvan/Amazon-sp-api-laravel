<form action="{{ route('getItemOffer') }}" method='GET'>
    @csrf
    <textarea name='asin_values' rows="10" cols="50"></textarea>
    <br>
    <label for=""> Item Condition </label>
    <select name='item_condition' >
        <option value="New">New</option>
        <option value="Used">Used</option>
        <option value="Collectible">Collectible</option>
        <option value="Refurbished">Refurbished</option>
        <option value="Club">Club</option>
    </select>

    <button type="submit"> Submit</button>

</form>