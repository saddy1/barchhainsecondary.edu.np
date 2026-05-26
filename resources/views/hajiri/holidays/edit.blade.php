<form method="post" action="{{route('hajiri.holidays.update',$holidayD)}}">
    @csrf
    @method('PUT')
    <div class="row">
        <label for="date" class="col-4 h5 p-2">Date for Holiday</label>
        <div class="col-8">
            <input class="form-control" readonly type="text" name="date" value="{{$holidayD['date']}}" placeholder="Date Holiday"/>   
        </div>
    </div>
    <div class="row pt-1">
        <label for="date" class="col-4 h5 p-2">Name of Holiday</label>
        <div class="col-8">
            <input class="form-control" type="text" name="name" value="{{$holidayD['label']}}" placeholder="Name of Holiday"/>   
        </div>
    </div>
    <input type="hidden" name="dsa" value="{{ $holidayD['dsa'] ?? 0 }}">
    <hr/>
    <div class="row pt-1">
        <div class="col-12 text-right">
            <button class="btn w-100 btn-primary rounded-0" type="submit"><i class="fa fa-edit"></i> Edit Holiday</button>
        </div>
    </div> 
</form>
