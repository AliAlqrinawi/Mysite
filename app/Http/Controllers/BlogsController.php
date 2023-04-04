<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class BlogsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Blog::get();
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('image', function ($row) {
                    $image = '<img src="' . asset('/') . $row->image . '" alt="image" width="50" height="50">';
                    return $image;
                })
                ->addColumn('status', function ($row) {
                    if ($row->status == 'ACTIVE') {
                        $status = '<button class="modal-effect btn btn-sm btn-success" style="margin: 5px" id="status" data-id="' . $row->id . '" ><i class=" icon-check"></i></button>';
                    } else {
                        $status = '<button class="modal-effect btn btn-sm btn-danger" style="margin: 5px" id="status" data-id="' . $row->id . '" ><i class=" icon-check"></i></button>';
                    }
                    return $status;
                })
                ->addColumn('action', function ($row) {
                    $btn = '<button class="modal-effect btn btn-sm btn-info"  style="margin: 5px" id="showModalEditBlog" data-id="' . $row->id . '"><i class="las la-pen"></i></button>';
                    $btn = $btn . '<button class="modal-effect btn btn-sm btn-danger" id="showModalDeleteBlog" data-name="' . $row->title_en . '" data-id="' . $row->id . '"><i class="las la-trash"></i></button>';
                    return $btn;
                })
                ->rawColumns([
                    'image' => 'image',
                    'status' => 'status',
                    'action' => 'action',
                ])
                ->make(true);
        }
        return view('dashboard.views-dash.blog.index');
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
        $blogData = $request->all();
        $validator = Validator($blogData, [
            'title_en' => 'required|string|min:3|max:255',
            'answer_en' => 'required|string|min:3|max:255',
            'question_en' => 'required|string|min:3|max:255',
            'image' => 'required|image',
            'status' => 'required|in:ACTIVE,NACTIVE',
        ]);
        if (!$validator->fails()) {
            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $imageName = time() . '_' . '.' . $image->getClientOriginalExtension();
                $image->move(public_path('image'), $imageName);
                $blogData['image'] = 'image/' . $imageName;
            }
            $blog = Blog::create($blogData);
                $response = [
                    'message' => 'Added successfully',
                    'status' => 200,
                ];
                return ControllersService::responseSuccess($response);
        } else {
            $response = [
                'message' => $validator->getMessageBag()->first(),
                'status' => 400,
            ];
            return ControllersService::responseErorr($response);
        }
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
        $blog = Blog::find($id);
        if($blog){
            $response = [
                'message' => 'Found Data',
                'status' => 200,
                'data' => $blog
            ];
            return ControllersService::responseSuccess($response);
        }else{
            $response = [
                'message' => 'Not Found Data',
                'status' => 400,
            ];
            return ControllersService::responseErorr($response);
        }
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
        $blogData = $request->all();
        $validator = Validator($blogData, [
            'title_en' => 'required|string|min:3|max:255',
            'answer_en' => 'required|string|min:3|max:255',
            'question_en' => 'required|string|min:3|max:255',
            'image' => 'nullable|image',
            'status' => 'required|in:ACTIVE,NACTIVE',
        ]);
        if (!$validator->fails()) {
            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $imageName = time() . '_' . '.' . $image->getClientOriginalExtension();
                $image->move(public_path('image'), $imageName);
                $blogData['image'] = 'image/' . $imageName;
            }
            $blog = Blog::find($id)->update($blogData);
                $response = [
                    'message' => 'Added successfully',
                    'status' => 200,
                ];
                return ControllersService::responseSuccess($response);
        } else {
            $response = [
                'message' => $validator->getMessageBag()->first(),
                'status' => 400,
            ];
            return ControllersService::responseErorr($response);
        }
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $blog = Blog::find($id);
        if($blog){
            $blog->delete();
            $response = [
                'message' => 'Deleted successfully',
                'status' => 200,
            ];
            return ControllersService::responseSuccess($response);
        }else{
            $response = [
                'message' => 'Not Found Data',
                'status' => 400,
            ];
            return ControllersService::responseErorr($response);
        }
    }

    public function status($id)
    {
        $blog = Blog::find($id);
        if($blog){
            $blog->changeStatus();
            $response = [
                'message' => 'Updated successfully',
                'status' => 200,
            ];
            return ControllersService::responseSuccess($response);
        }else{
            $response = [
                'message' => 'Not Found Data',
                'status' => 400,
            ];
            return ControllersService::responseErorr($response);
        }
    }
}
