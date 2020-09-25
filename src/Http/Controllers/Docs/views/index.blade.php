@view('ajax')
<div class="doc-hub-outer-container">
    <div class="docs-hub">
        <div class="container-fluid">
            <div class="row">
                <div class="col-8">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="#">Home</a>
                        </li>
                        <li class="breadcrumb-item">{{ $title }}</li>
                    </ol>
                </div>
                <div class="col-4">
                    <div class="switcher pull-right">
                        <div class="dropdown">
                            <button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true"
                                aria-expanded="false">
                                {{ $version }}
                            </button>
                            <div class="dropdown-menu">
                                @foreach ($versions as $name => $version)
                                <a class="dropdown-item" href="{{ url('docs/'.$version) }}">{{ $name }}</a>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="container-fluid">
        <div class="row doc-hub-content-container">
            <div class="col doc-hub-sidebar" data-stickys="100" data-pjax>
                <div class="doc-hub-container">
                    <div class="doc-hub-items" id="doc-hub-indexes">{!! $index !!}</div>
                </div>
            </div>
            <div class="col doc-hub-page" data-pjax-container>
                @endview
                <div class="row">
                    <div class="col">
                        <div class="doc-hub-body">
                            <title>{{ $title }}</title>
                            {!! $content !!}
                        </div>
                    </div>
                    <div class="col doc-hub-sections" data-sticky="100">
                        <div class="doc-hub-container">
                            <div class="doc-hub-sections-items">
                                <div class="doc-hub-sections-title">
                                    <a href="#">
                                        <i class="fa fa-align-left"></i> Table of Contents</a>
                                </div>
                                <ul role="page-nav">
                                    @foreach ($sections as $item)
                                    <li>
                                        <a href="{{ $item['url'] }}">{{ $item['name'] }}</a>
                                        @if ($item['childs'])
                                        <ul>
                                            @foreach ($item['childs'] as $child)
                                            <li>
                                                <a href="{{ url($child['url']) }}">{{ $child['name'] }}</a>
                                                @if ($child['childs'])
                                                <ul>
                                                    @foreach ($child['childs'] as $gchild)
                                                    <li>
                                                        <a href="{{ $gchild['url'] }}">{{ $gchild['name'] }}</a>
                                                    </li>
                                                    @endforeach
                                                </ul>
                                                @endif
                                            </li>
                                            @endforeach
                                        </ul>
                                        @endif
                                    </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                @view('ajax')
            </div>
        </div>
    </div>
</div>
@endview