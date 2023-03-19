<div class="ibox ">
    <div class="ibox-title">
        <h5>Category List</h5>
        <div class="ibox-tools">
            <a class="collapse-link">
                <i class="fa fa-chevron-up"></i>
            </a>
            <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                <i class="fa fa-wrench"></i>
            </a>
            <ul class="dropdown-menu dropdown-user">
                <li><a href="#" class="dropdown-item">Config option 1</a>
                </li>
                <li><a href="#" class="dropdown-item">Config option 2</a>
                </li>
            </ul>
            <a class="close-link">
                <i class="fa fa-times"></i>
            </a>
        </div>
    </div>

    <div class="ibox-content table-responsive">
        <div class="row justify-content-between mb-3">
            <div class="col-md-1">
                <select class="form-control" wire:model="perPage">
                    <option value="10">10</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                </select>
            </div>
            <div class="col-md-3">
                <input type="text" class="form-control" wire:model.debounce.500ms="search" placeholder="cari">
            </div>
        </div>
        <table class="table table-hover">
            <thead>
                <tr>
                    <th width="5%">#</th>
                    <th width="30%">Nama Kategori</th>
                    <th width="20%">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @if (count($categories) > 0)
                    @foreach ($categories as $category)
                        <tr>
                            <td>#</td>
                            <td>{{ $category->name }}</td>
                            <td>
                                <form method="POST" action="{{ route('category.destroy', $category->id) }}">
                                    @method('DELETE')
                                    @csrf
                                    <a class="btn btn-sm btn-secondary"
                                        href="{{ route('category.show', $category->id) }}"><i class="fa fa-eye"></i></a>
                                    <a class="btn btn-sm btn-primary"
                                        href="{{ route('category.edit', $category->id) }}"><i
                                            class="fa fa-edit"></i></a>
                                    <button class="btn btn-sm btn-danger"><i class="fa fa-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="4" class="text-center">Data tidak ditemukan</td>
                    </tr>
                @endif
            </tbody>
        </table>
        {{ $categories->links() }}
    </div>
</div>
