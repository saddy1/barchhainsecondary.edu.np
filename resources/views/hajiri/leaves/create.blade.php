<form method="post" action="{{route('hajiri.leaves.store')}}">
    @csrf
    <div class="row">
        <label for="date" class="col-4 h5 p-2">Date for Holiday</label>
        <div class="col-4">
            <input id="from" class="date-picker form-control" type="text" name="from" value="{{$date}}" placeholder="From"/>   
        </div>
        <div class="col-4">
            <input id="to" class="date-picker form-control" type="text" name="to" value="{{$date}}" placeholder="To"/>   
        </div>
    </div>
    <div class="row pt-1">
        <label for="type" class="col-4 h5 p-2">Employee</label>
        <div class="col-8">
            <select name="user_id"  class="form-control">
                <option value="" disabled selected>Select Employee</option>
                @foreach($users as $user)
                    <option value="{{$user['id']}}">{{$user['name']}}</option>
                @endforeach
            </select>
        </div>
    </div>
    <div class="row pt-1">
        <label for="type" class="col-4 h5 p-2">Type of leave</label>
        <div class="col-8">
            <select name="leave_id"  class="form-control">
                <option value="" disabled selected>Select Type of Leave</option>
                @foreach($leave_type as $lt)
                    <option value="{{$lt['id']}}">{{$lt['name']}}</option>
                @endforeach
            </select>
        </div>
    </div>
    <hr/>
    <div class="row pt-1">
        <div class="col-12 text-right">
            <button class="btn w-100 btn-primary rounded-0" type="submit"><i class="fa fa-plus"></i> Add Holiday</button>
        </div>
    </div> 
</form>
