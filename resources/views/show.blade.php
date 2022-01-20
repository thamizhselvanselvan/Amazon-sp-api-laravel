<form action="{{ route('showInput') }}" method='post'>
    @csrf
    <textarea name='asinText' rows="10" cols="100">
    </textarea>
    <button type='submit'> submit</button>
</form>
