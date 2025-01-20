<?php

namespace App\Http\Controllers;

use App\Http\Requests\UploadRequest;
use App\Models\Upload;
use Illuminate\Http\Request;

class UploadController extends Controller
{
    public function index()
    {
        $uploads = Upload::all();
        return view('upload.index', compact('uploads'));
    }

    public function create()
    {
        return view('upload.create');
    }

    public function store(UploadRequest $request)
    {
        $file = $request->file('file');
        $original_name = $file->getClientOriginalName();

        $name = date("Ymd")."_".$file->getClientOriginalName();

        $tujuan_upload = 'storage';
        $file->move($tujuan_upload,$name);

        if (!empty($request->file('file2'))){
            $file2 = $request->file('file2');
            $original_name2 = $file2->getClientOriginalName();

            $name2 = date("Ymd")."_".$file2->getClientOriginalName();

            $file2->move($tujuan_upload,$name2);
        }

        $file3 = $request->file('file3');
        $original_name3 = $file3->getClientOriginalName();

        $name3 = date("Ymd")."_".$file3->getClientOriginalName();

        $file3->move($tujuan_upload,$name3);

        $file4 = $request->file('file4');
        $original_name4 = $file4->getClientOriginalName();

        $name4 = date("Ymd")."_".$file4->getClientOriginalName();

        $file4->move($tujuan_upload,$name4);

        $file5 = $request->file('file5');
        $original_name5 = $file5->getClientOriginalName();

        $name5 = date("Ymd")."_".$file5->getClientOriginalName();

        $file5->move($tujuan_upload,$name5);

        $data = $request->validated();

        Upload::create([
            'title' => $data['title'],
            'file' => $original_name,
            'path' => $name,
            'file2' => $original_name2,
            'path2' => $name2,
            'file3' => $original_name3,
            'path3' => $name3,
            'file4' => $original_name4,
            'path4' => $name4,
            'file5' => $original_name5,
            'path5' => $name5,
        ]);

        return redirect()->route('upload.index');
    }

    public function show(Upload $upload)
    {
        //
    }

    public function edit(Upload $upload)
    {
        return view('upload.edit', compact('upload'));
    }

    public function update(UploadRequest $request, Upload $upload)
    {
        if (!empty($request->file('file')))
        {
            $file = $request->file('file');
            $original_name = $file->getClientOriginalName();

            $name = date("Ymd")."_".$file->getClientOriginalName();

            $tujuan_upload = 'storage';
            $file->move($tujuan_upload,$name);

            $file2 = $request->file('file2');
            $original_name2 = $file2->getClientOriginalName();

            $name2 = date("Ymd")."_".$file2->getClientOriginalName();

            $file2->move($tujuan_upload,$name2);

            $file3 = $request->file('file3');
            $original_name3 = $file3->getClientOriginalName();

            $name3 = date("Ymd")."_".$file3->getClientOriginalName();

            $file3->move($tujuan_upload,$name3);

            $file4 = $request->file('file4');
            $original_name4 = $file4->getClientOriginalName();

            $name4 = date("Ymd")."_".$file4->getClientOriginalName();

            $file4->move($tujuan_upload,$name4);

            $file5 = $request->file('file5');
            $original_name5 = $file5->getClientOriginalName();

            $name5 = date("Ymd")."_".$file5->getClientOriginalName();

            $file5->move($tujuan_upload,$name5);

            $data = $request->validated();

            $upload->update([
                'title' => $data['title'],
                'file' => $original_name,
                'path' => $name,
                'file2' => $original_name2,
                'path2' => $name2,
                'file3' => $original_name3,
                'path3' => $name3,
                'file4' => $original_name4,
                'path4' => $name4,
                'file5' => $original_name5,
                'path5' => $name5,
            ]);
        }
        else
        {
            $data = $request->validated();

            $upload->update([
                'title' => $data['title'],
            ]);
        }
        return redirect()->route('upload.index');
    }

    public function destroy(Upload $upload)
    {
        $upload->delete();

        return redirect()->route('upload.index');
    }
}
