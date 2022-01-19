
<form action="{{ route('getPrice') }}" method='GET'>
    @csrf
    <textarea name='identity_values' rows="10" cols="50"></textarea>
    <br>
    <label for="">Item Type</label>
    <select name='identity_type' >
        <option value="Asin">Asin</option>
        <option value="Skus">Skus</option>
    </select>

    <button type="submit"> Submit</button>

</form>