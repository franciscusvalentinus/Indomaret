<div class="relative md:pt-32 pb-32 pt-12">
    <div class="px-4 md:px-10 mx-auto w-full">
        <div>
            <div class="max-w-4xl mx-auto py-10 sm:px-6 lg:px-8">
                <div class="mt-5 md:mt-0 md:col-span-2">
                    <form method="post" action="{{ route('upload.update', $upload->id) }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="shadow overflow-hidden sm:rounded-md">
                            <div class="px-4 py-5 bg-white sm:p-6">
                                <label for="title" class="block font-medium text-sm text-gray-700">Title</label>
                                <input type="text" name="title" id="title" class="form-input rounded-md shadow-sm mt-1 block w-full"
                                       value="{{ old('title', $upload->title) }}" />
                                @error('title')
                                <p class="text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="px-4 py-5 bg-white sm:p-6">
                                File <a href="/storage/{{ $upload->path }}" class="text-indigo-600 hover:text-indigo-900 mb-2 mr-2">{{ $upload->file }}</a>
                            </div>

                            <div class="px-4 py-5 bg-white sm:p-6">
                                File 2 <a href="/storage/{{ $upload->path2 }}" class="text-indigo-600 hover:text-indigo-900 mb-2 mr-2">{{ $upload->file2 }}</a>
                            </div>

                            <div class="px-4 py-5 bg-white sm:p-6">
                                File 3 <a href="/storage/{{ $upload->path3 }}" class="text-indigo-600 hover:text-indigo-900 mb-2 mr-2">{{ $upload->file3 }}</a>
                            </div>

                            <div class="px-4 py-5 bg-white sm:p-6">
                                File 4 <a href="/storage/{{ $upload->path4 }}" class="text-indigo-600 hover:text-indigo-900 mb-2 mr-2">{{ $upload->file4 }}</a>
                            </div>

                            <div class="px-4 py-5 bg-white sm:p-6">
                                File 5 <a href="/storage/{{ $upload->path5 }}" class="text-indigo-600 hover:text-indigo-900 mb-2 mr-2">{{ $upload->file5 }}</a>
                            </div>

                            <div class="px-4 py-5 bg-white sm:p-6">
                                <label for="file" class="block font-medium text-sm text-gray-700">Or Upload New File</label>
                                <input type="file" name="file" id="file" class="form-input rounded-md shadow-sm mt-1 block w-full"
                                       value="{{ old('file', $upload->file) }}" />
                                @error('file')
                                <p class="text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="px-4 py-5 bg-white sm:p-6">
                                <label for="file2" class="block font-medium text-sm text-gray-700">Or Upload New File 2</label>
                                <input type="file" name="file2" id="file2" class="form-input rounded-md shadow-sm mt-1 block w-full"
                                       value="{{ old('file2', $upload->file2) }}" />
                                @error('file2')
                                <p class="text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="px-4 py-5 bg-white sm:p-6">
                                <label for="file3" class="block font-medium text-sm text-gray-700">Or Upload New File 3</label>
                                <input type="file" name="file3" id="file3" class="form-input rounded-md shadow-sm mt-1 block w-full"
                                       value="{{ old('file3', $upload->file3) }}" />
                                @error('file3')
                                <p class="text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="px-4 py-5 bg-white sm:p-6">
                                <label for="file4" class="block font-medium text-sm text-gray-700">Or Upload New File 4</label>
                                <input type="file" name="file4" id="file4" class="form-input rounded-md shadow-sm mt-1 block w-full"
                                       value="{{ old('file4', $upload->file4) }}" />
                                @error('file4')
                                <p class="text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="px-4 py-5 bg-white sm:p-6">
                                <label for="file5" class="block font-medium text-sm text-gray-700">Or Upload New File 5</label>
                                <input type="file" name="file5" id="file5" class="form-input rounded-md shadow-sm mt-1 block w-full"
                                       value="{{ old('file5', $upload->file5) }}" />
                                @error('file5')
                                <p class="text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="flex items-center justify-end px-4 py-3 bg-gray-50 text-right sm:px-6">
                                <button class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:shadow-outline-gray disabled:opacity-25 transition ease-in-out duration-150">
                                    Edit
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
