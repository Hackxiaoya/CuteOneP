@extends('front.default.public.layout')

@section('style')
    <style>
        .other_btn {
            position: absolute;
            left: 0;
        }
        .other_btn span {
            margin-left: 10px;
        }
    </style>
@endsection


@section('content')
    <div class="mdui-container-fluid">
        <div class="mdui-toolbar nexmoe-item">
            <a href="/disk/{{ $disk_id }}">根目录</a>/
            @foreach ($crumbs_list as $v)
                <a href="{{ $crumbs_url }}/{{ urlencode($v['path']) }}">{{ $v['name'] }}</a>/
            @endforeach
        </div>
    </div>
    <div class="mdui-container-fluid">
        <div class="nexmoe-item">
            <div class="mdui-row">
                <ul class="mdui-list">
                    <li class="mdui-list-item th">
                        <div class="mdui-col-xs-12 mdui-col-sm-7">文件 <i class="mdui-icon material-icons icon-sort" data-table="name" data-sort="more">expand_more</i></div>
                        <div class="mdui-col-sm-3 mdui-text-right">修改时间 <i class="mdui-icon material-icons icon-sort" data-table="lastModifiedDateTime" data-sort="more">expand_more</i></div>
                        <div class="mdui-col-sm-2 mdui-text-right">大小 <i class="mdui-icon material-icons icon-sort" data-table="size" data-sort="more">expand_more</i></div>
                    </li>
                    @foreach ($data['data'] as $v)
                        <li class="mdui-list-item mdui-ripple">
                            @if ($v->file_type == 'folder')
                                <a href="{{ $current_url }}/{{ urlencode(str_replace('+', '%2B', $v->name)) }}">
                                    <div class="mdui-col-xs-12 mdui-col-sm-7 mdui-text-truncate">
                                        <i class="mdui-icon material-icons">folder_open</i>
                                        <span>{{ $v->name }}</span>
                                    </div>
                                    <div class="mdui-col-sm-3 mdui-text-right">{{ $v->lastModifiedDateTime }}</div>
                                    <div class="mdui-col-sm-2 mdui-text-right">{{ $v->size }}</div>
                                </a>
                            @elseif ($v->file_type == 'image/jpeg' || $v->file_type == 'image/png')
                                <a href="javascript:void(0);">
                                    <div _src="/disk/thumbnails/{{ $disk_id }}/{{ $v->file_id }}" class="mdui-col-xs-12 mdui-col-sm-7 mdui-text-truncate lightboxurl" data-lightbox="lightbox">
                                        <i class="mdui-icon material-icons">image</i>
                                        <span>{{ $v->name }}</span>
                                    </div>
                                    <div class="mdui-col-sm-3 mdui-text-right">
                                        <div class="other_btn">
                                            <span class="down_file down_file_icon" data-disk="{{ $disk_id }}" data-id="{{ $v->file_id }}" data-name="{{ $v->name }}">
                                                    <i class="mdui-icon material-icons">cloud_download</i>
                                            </span>
                                            <span class="link_file down_file_icon" data-disk="{{ $disk_id }}" data-id="{{ $v->file_id }}" data-name="{{ $v->name }}">
                                                    <i class="mdui-icon material-icons">link</i>
                                            </span>
                                        </div>
                                        {{ $v->lastModifiedDateTime }}
                                    </div>
                                    <div class="mdui-col-sm-2 mdui-text-right">{{ $v->size }}</div>
                                </a>
                            @elseif ($v->file_type == 'audio/mpeg' || $v->file_type == 'audio/x-flac' || $v->file_type == 'audio/x-wav' || $v->file_type == 'audio/mp4')
                                <a href="javascript:void(0);" class="@if ( $webConfig['is_music'] == 1)addMusicList @else video_open @endif" data-disk="{{ $disk_id }}" data-id="{{ $v->file_id }}" data-name="{{ $v->name }}">
                                    <div class="mdui-col-xs-12 mdui-col-sm-7 mdui-text-truncate">
                                        <i class="mdui-icon material-icons">music_note</i>
                                        <span>{{ $v->name }}</span>
                                    </div>
                                    <div class="mdui-col-sm-3 mdui-text-right">
                                        <div class="other_btn">
                                            <span class="down_file down_file_icon" data-disk="{{ $disk_id }}" data-id="{{ $v->file_id }}" data-name="{{ $v->name }}">
                                                    <i class="mdui-icon material-icons">cloud_download</i>
                                            </span>
                                            <span class="link_file down_file_icon" data-disk="{{ $disk_id }}" data-id="{{ $v->file_id }}" data-name="{{ $v->name }}">
                                                    <i class="mdui-icon material-icons">link</i>
                                            </span>
                                        </div>
                                        {{ $v->lastModifiedDateTime }}
                                    </div>
                                    <div class="mdui-col-sm-2 mdui-text-right">{{ $v->size }}</div>
                                </a>
                            @elseif ($v->file_type == 'video/mp4' || $v->file_type == 'video/x-matroska' || $v->file_type == 'application/octet-stream')
                                <a href="javascript:void(0);" class="video_open" data-disk="{{ $disk_id }}" data-id="{{ $v->file_id }}" data-name="{{ $v->name }}">
                                    <div class="mdui-col-xs-12 mdui-col-sm-7 mdui-text-truncate">
                                        <i class="mdui-icon material-icons">videocam</i>
                                        <span>{{ $v->name }}</span>
                                    </div>
                                    <div class="mdui-col-sm-3 mdui-text-right">
                                        <div class="other_btn">
                                            <span class="down_file down_file_icon"  data-disk="{{ $disk_id }}" data-id="{{ $v->file_id }}" data-name="{{ $v->name }}">
                                                    <i class="mdui-icon material-icons">cloud_download</i>
                                            </span>
                                            <span class="link_file down_file_icon" data-disk="{{ $disk_id }}" data-id="{{ $v->file_id }}" data-name="{{ $v->name }}">
                                                    <i class="mdui-icon material-icons">link</i>
                                            </span>
                                        </div>
                                        {{ $v->lastModifiedDateTime }}
                                    </div>
                                    <div class="mdui-col-sm-2 mdui-text-right">{{ $v->size }}</div>
                                </a>
                            @elseif ($v->file_type == 'application/msword' || $v->file_type == 'application/vnd.ms-excel' || $v->file_type == 'application/application/pdf')
                                <a href="javascript:void(0);" class="open_word" data-disk="{{ $disk_id }}" data-id="{{ $v->file_id }}" data-name="{{ $v->name }}">
                                    <div class="mdui-col-xs-12 mdui-col-sm-7 mdui-text-truncate">
                                        <i class="mdui-icon material-icons">description</i>
                                        <span>{{ $v->name }}</span>
                                    </div>
                                    <div class="mdui-col-sm-3 mdui-text-right">
                                        <div class="other_btn">
                                            <span class="down_file down_file_icon" data-disk="{{ $disk_id }}" data-id="{{ $v->file_id }}" data-name="{{ $v->name }}">
                                                    <i class="mdui-icon material-icons">cloud_download</i>
                                            </span>
                                            <span class="link_file down_file_icon" data-disk="{{ $disk_id }}" data-id="{{ $v->file_id }}" data-name="{{ $v->name }}">
                                                    <i class="mdui-icon material-icons">link</i>
                                            </span>
                                        </div>
                                        {{ $v->lastModifiedDateTime }}
                                    </div>
                                    <div class="mdui-col-sm-2 mdui-text-right">{{ $v->size }}</div>
                                </a>
                            @elseif ($v->file_type == 'application/zip')
                                <a href="javascript:void(0);" class="down_file" data-disk="{{ $disk_id }}" data-id="{{ $v->file_id }}" data-name="{{ $v->name }}">
                                    <div class="mdui-col-xs-12 mdui-col-sm-7 mdui-text-truncate">
                                        <i class="mdui-icon material-icons">view_day</i>
                                        <span>{{ $v->name }}</span>
                                    </div>
                                    <div class="mdui-col-sm-3 mdui-text-right">
                                        <div class="other_btn">
                                            <span class="down_file down_file_icon" data-disk="{{ $disk_id }}" data-id="{{ $v->file_id }}" data-name="{{ $v->name }}">
                                                    <i class="mdui-icon material-icons">cloud_download</i>
                                            </span>
                                            <span class="link_file down_file_icon" data-disk="{{ $disk_id }}" data-id="{{ $v->file_id }}" data-name="{{ $v->name }}">
                                                    <i class="mdui-icon material-icons">link</i>
                                            </span>
                                        </div>
                                        {{ $v->lastModifiedDateTime }}
                                    </div>
                                    <div class="mdui-col-sm-2 mdui-text-right">{{ $v->size }}</div>
                                </a>
                            @else
                                <a href="javascript:void(0);" class="down_file" data-disk="{{ $disk_id }}" data-id="{{ $v->file_id }}" data-name="{{ $v->name }}">
                                    <div class="mdui-col-xs-12 mdui-col-sm-7 mdui-text-truncate">
                                        <i class="mdui-icon material-icons">view_day</i>
                                        <span>{{ $v->name }}</span>
                                    </div>
                                    <div class="mdui-col-sm-3 mdui-text-right">
                                        <div class="other_btn">
                                            <span class="down_file down_file_icon" data-disk="{{ $disk_id }}" data-id="{{ $v->file_id }}" data-name="{{ $v->name }}">
                                                    <i class="mdui-icon material-icons">cloud_download</i>
                                            </span>
                                            <span class="link_file down_file_icon" data-disk="{{ $disk_id }}" data-id="{{ $v->file_id }}" data-name="{{ $v->name }}">
                                                    <i class="mdui-icon material-icons">link</i>
                                            </span>
                                        </div>
                                        {{ $v->lastModifiedDateTime }}
                                    </div>
                                    <div class="mdui-col-sm-2 mdui-text-right">{{ $v->size }}</div>
                                </a>
                            @endif
                        </li>
                    @endforeach
                </ul>

                <div class="page-box">
                    <ul>
                        <li>
                            <span data-page="1" class="page-click">
                                首页
                            </span>
                        </li>
                        @foreach ($data['pagination']['page'] as $v)
                            @if ($v == '...')
                                <li style="width: 20px;line-height: 30px;">
                                    ...
                                </li>
                            @else
                                <li class="@if ($v == $data['pagination']['now_page']) current @endif">
                                    <span data-page="{{ $v }}" class="@if ($v != $data['pagination']['now_page']) page-click @endif">
                                        {{ $v }}
                                    </span>
                                </li>
                            @endif
                        @endforeach
                        <li>
                            <i class="allpage">
                                共 {{ $data['pagination']['count'] }} 页
                            </i>
                        </li>
                        <li>
                            <span data-page="{{ $data['pagination']['count'] }}" class="page-click">
                                尾页
                            </span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
@endsection


@section('script')
@endsection