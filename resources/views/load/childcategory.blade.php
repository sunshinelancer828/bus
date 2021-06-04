<option value="">Select Child Category</option>
@foreach($subcat->childs->sortBy('name') as $child)
<option value="{{ $child->id }}">{{ $child->name }}</option>
@endforeach