@extends('products.layout')

@section('content')

<div class="container mt-4">


    {{-- Success --}}

    @if(session('success'))

    <div class="alert alert-success">

        {{ session('success') }}

    </div>

    @endif


    {{-- Header --}}

    <div class="d-flex justify-content-between mb-3">

        <h2>
            Categories
        </h2>


        <a href="{{ route('categories.create') }}"
            class="btn btn-primary">

            Add Category

        </a>

    </div>


    {{-- Search / Filter --}}

    <form method="GET"
        class="row g-2 mb-3">


        {{-- Search --}}

        <div class="col-md-5">

            <input type="text"
                name="q"
                value="{{ request('q') }}"
                class="form-control"
                placeholder="Search category / slug / description">

        </div>


        {{-- Status --}}

        <div class="col-md-3">

            <select name="status"
                class="form-control">

                <option value="">
                    All Status
                </option>

                <option value="1"
                    {{ request('status') === '1' ? 'selected' : '' }}>

                    Active

                </option>

                <option value="0"
                    {{ request('status') === '0' ? 'selected' : '' }}>

                    Inactive

                </option>

            </select>

        </div>


        {{-- Sort --}}

        <div class="col-md-3">

            <select name="sort"
                class="form-control">

                <option value="">
                    Newest
                </option>

                <option value="oldest"
                    {{ request('sort') === 'oldest' ? 'selected' : '' }}>

                    Oldest

                </option>

                <option value="name_asc"
                    {{ request('sort') === 'name_asc' ? 'selected' : '' }}>

                    Name A-Z

                </option>

                <option value="name_desc"
                    {{ request('sort') === 'name_desc' ? 'selected' : '' }}>

                    Name Z-A

                </option>

            </select>

        </div>


        <div class="col-md-1">

            <button class="btn btn-dark w-100">

                Go

            </button>

        </div>

    </form>


    {{-- Table --}}

    <div class="table-responsive">

        <table class="table table-bordered table-striped">

            <thead>

                <tr>

                    <th>
                        #ID
                    </th>

                    <th>
                        Name
                    </th>

                    <th>
                        Slug
                    </th>

                    <th>
                        Description
                    </th>

                    <th>
                        Status
                    </th>

                    <th width="160">
                        Actions
                    </th>

                </tr>

            </thead>


            <tbody>

                @forelse($categories as $cat)

                <tr>

                    <td>
                        {{ $cat->id }}
                    </td>


                    <td>
                        {{ $cat->name }}
                    </td>


                    <td>
                        {{ $cat->slug }}
                    </td>


                    <td>

                        {{ Str::limit($cat->description, 50) }}

                    </td>


                    <td>

                        @if($cat->status)

                        <span class="badge bg-success">
                            Active
                        </span>

                        @else

                        <span class="badge bg-danger">
                            Inactive
                        </span>

                        @endif

                    </td>


                    <td>

                        <a href="{{ route('categories.edit', $cat) }}"
                            class="btn btn-warning btn-sm">

                            Edit

                        </a>


                        <form action="{{ route('categories.destroy', $cat) }}"
                            method="POST"
                            style="display:inline-block;">

                            @csrf
                            @method('DELETE')

                            <button class="btn btn-danger btn-sm"
                                onclick="return confirm('Delete category?')">

                                Delete

                            </button>

                        </form>

                    </td>

                </tr>

                @empty

                <tr>

                    <td colspan="6"
                        class="text-center">

                        No categories

                    </td>

                </tr>

                @endforelse

            </tbody>

        </table>

    </div>


    {{ $categories->links() }}

</div>

@endsection