@foreach($permissionGroupList as $key => $value)
    <div class="card permission-group-card m-b-20" style="border: none; border-radius: 14px; box-shadow: 0 4px 15px rgba(0,0,0,0.04); background: #fff; overflow: hidden;">
        <div class="card-header border-bottom p-3 d-flex align-items-center justify-content-between" style="background: #f8fafc;">
            <div class="d-flex align-items-center">
                <div class="m-r-10" style="width: 32px; height: 32px; border-radius: 8px; background: #e0e7ff; color: #4338ca; display: flex; align-items: center; justify-content: center; font-size: 14px;">
                    <i class="ti-shield"></i>
                </div>
                <h5 class="card-title m-b-0" style="font-weight: 700; color: #1e293b; font-size: 15px;">{{ $value }}</h5>
            </div>
            <span class="badge badge-light" style="font-size: 11px; padding: 4px 10px; border-radius: 6px; color: #64748b;">
                {{ count($commonPermission[$key] ?? []) }} quyền hạn
            </span>
        </div>
        <div class="card-body p-4">
            <div class="row">
                @foreach($commonPermission[$key] as $permission)
                    <div class="col-md-4 col-sm-6 m-b-15">
                        <div class="permission-item-box p-3" style="border: 1px solid #f1f5f9; border-radius: 10px; background: #fafafa; display: flex; align-items: center; justify-content: space-between; transition: all 0.2s;">
                            <label for="switch-{{ $permission->id }}" class="m-b-0" style="font-size: 13px; font-weight: 500; color: #334155; cursor: pointer; flex: 1; padding-right: 12px; line-height: 1.4;">
                                {{ $permission->vi_content ?: $permission->content }}
                            </label>
                            <div class="switch switch-info d-inline m-0">
                                <input class="permission-checkbox" type="checkbox"
                                       id="switch-{{ $permission->id }}" disabled="">
                                <label for="switch-{{ $permission->id }}"></label>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@endforeach
