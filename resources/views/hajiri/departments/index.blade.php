@extends('hajiri.layouts.app')

@section('content')
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg container-fluid py-4">
                <div class="h4 pb-2">Department List</div>
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                @if (Session::has('message'))
                    <div class="alert alert-info">{{ Session::get('message') }}</div>
                @endif
                <div class="row px-2">
                    <table id="example" class="table table-striped table-bordered w-100">
                        <thead>
                            <tr>
                                <th>SN</th>
                                <th>Name of Department</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                        @foreach($department as $key=>$department_)
                            @if($department_['alias'] == 'DEFAULT')
                            @continue
                        @endif
                            <tr>
                                <form method="post" action="{{route('hajiri.department.update',$department_->id)}}">
                                @csrf
                                @method('put')
                                <input value="{{($department_['status'])?1:0}}" name="status" type="hidden"/>
                                <td>{{$key}}</td>
                                <td>
                                    <span id="label_{{$key}}">{{$department_['label']}}</span>
                                    <input  id="input_{{$key}}" value="{{$department_['label']}}" class="form-control d-none" type="text" name="name" placeholder="Enter Department Name" required/>
                                </td>
                                <td class="text-center">
                                    <button type="button" onclick="$('#submitStatus_{{$key}}').submit();" class="btn"><i class="fa {{($department_['status'] == 1)?'fa-check-circle text-success':'fa-times-circle fa-danger'}}"></i></button>
                                </td>
                                <td class="text-right">
                                <!-- href="{{route('hajiri.department.edit',$department_)}}" -->
                                        <button onclick="$('#label_{{$key}}').addClass('d-none'); $('#input_{{$key}}').removeClass('d-none');$('#btn_{{$key}}').removeClass('d-none'); $(this).addClass('d-none')" class="w-100 btn btn-secondary" type="button"><i
                                                class="fa fa-edit"></i>&nbsp;&nbsp;Edit</button>
                                        <button id="btn_{{$key}}" class="w-100 btn btn-success d-none" type="submit"><i
                                                class="fa fa-save"></i>&nbsp;&nbsp;Save</button>
                                </td>
                                </form>
                                <form id="submitStatus_{{$key}}" method="post" action="{{route('hajiri.department.update',$department_->id)}}">
                                    @csrf
                                    @method('put')
                                    <input value="{{$department_['label']}}" type="hidden" name="name"/>
                                    <input value="{{($department_['status'])?0:1}}" name="status" type="hidden"/>
                                </form>
                            </tr>
                        @endforeach
                            <tr>
                                <form method="post" action="{{route('hajiri.department.store')}}">
                                @csrf
                                <td>#</td>
                                <td><input class="form-control" type="text" name="name" placeholder="Enter Department Name" required/></td>
                                <td class="text-center"><i class="fa fa-check-circle text-success"></i></td>
                                <td class="text-right">
                                    <button class="w-100 btn btn-info" type="submit"><i
                                            class="fa fa-plus-circle"></i>&nbsp;&nbsp;Add</button>
                                </td>
                                </form>
                            </tr>
                        </tbody>
                        <tfoot>
                            <tr>
                                <th>SN</th>
                                <th>Name of Department</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
