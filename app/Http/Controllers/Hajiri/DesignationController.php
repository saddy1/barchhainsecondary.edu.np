<?php

namespace App\Http\Controllers\Hajiri;

use Illuminate\Http\Request;
use Session;

use App\Models\Hajiri\Designation;

class DesignationController extends Controller
{
    private $designations;
    public function __construct(Designation $designations)
    {
        $this->designations = $designations;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $designation = $this->designations->get();
        return view('hajiri.designations.index',compact('designation'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|max:255',
        ]);

        $designation = $this->designations;
        $designation->label = $request->name;
        $designation->save();
        Session::flash('message', 'Designation Successfully Added!');
        return redirect()->back();
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'required|max:191',
            'status' => 'required',
        ]);

        $designation = $this->designations->find($id);
        $designation->update(['label'=>$request->name,'status'=>$request->status]);

        Session::flash('message', 'Designation Successfully Updated!');
        return redirect()->back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
