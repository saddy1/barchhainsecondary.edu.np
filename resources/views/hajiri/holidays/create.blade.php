<form method="post" action="{{route('hajiri.holidays.store')}}">
    @csrf
    <div class="row">
        <label for="date" class="col-4 h5 p-2">Date for Holiday</label>
        <div class="col-8">
            <input class="form-control" readonly type="text" name="date" value="{{$date}}" placeholder="Date Holiday"/>   
        </div>
    </div>
    <div class="row pt-1">
        <label for="date" class="col-4 h5 p-2">Name of Holiday</label>
        <div class="col-8">
            <input class="form-control" type="text" name="name" placeholder="Name of Holiday"/>   
        </div>
    </div>
    <input type="hidden" name="dsa" value="0">
    <hr/>
    <div class="row pt-1">
        <div class="col-12 text-right">
            <button class="btn w-100 btn-primary rounded-0" type="submit"><i class="fa fa-plus"></i> Add Holiday</button>
        </div>
    </div> 
</form>
