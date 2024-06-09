<div style="display: flex; align-items: center; justify-content: center; width: 100%">
    @can('edit', $data['entity'])
        <a href="javascript:void(0);" onClick="editFunc({{ $data['entity']->id  }})" class="m-1 btn text-success">
            <i class="fa fa-edit"></i>
        </a>
    @endcan
    @can('view', $data['entity'])
        <a href="{{ $data['route']  }}" class="m-1 btn text-secondary">
            <i class="fa fa-eye"></i>
        </a>
    @endcan
    @can('delete', $data['entity'])
        <a href="javascript:void(0);" onClick="deleteFunc({{ $data['entity']->id  }})" class="m-1 btn">
            <i class="fa fa-trash" style="color: #e74a3b"></i>
        </a>
    @endcan
</div>
