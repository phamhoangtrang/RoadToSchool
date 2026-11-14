@unless ($breadcrumbs->isEmpty())
    <div class="breadcrumbs-container">
        <div class="container">
            <nav aria-label="breadcrumb" class="modern-breadcrumb-nav">
                <ol class="breadcrumb modern-breadcrumb">
                    @foreach ($breadcrumbs as $breadcrumb)
                        @if ($loop->first)
                            <li class="breadcrumb-item">
                                <a href="{{ $breadcrumb->url ?? '/' }}">
                                    <i class="fa fa-home breadcrumb-home-icon"></i>
                                    <span>{{ $breadcrumb->title }}</span>
                                </a>
                            </li>
                        @elseif ($breadcrumb->url && !$loop->last)
                            <li class="breadcrumb-item">
                                <span class="breadcrumb-separator"><i class="fa fa-angle-right"></i></span>
                                <a href="{{ $breadcrumb->url }}">{{ $breadcrumb->title }}</a>
                            </li>
                        @else
                            <li class="breadcrumb-item active" aria-current="page">
                                <span class="breadcrumb-separator"><i class="fa fa-angle-right"></i></span>
                                <span class="active-title">{{ $breadcrumb->title }}</span>
                            </li>
                        @endif
                    @endforeach
                </ol>
            </nav>
        </div>
    </div>
@endunless
