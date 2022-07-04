@extends('adminlte::page')

@section('content')

<section class="border-bottom wow fadeIn">
    <div class="bg-light">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div id="accordion-7" class="accordion-style-7 bg-white shadow-v1">

                        @foreach($data as $k1 => $methods)
                        <div class="accordion-item border-bottom border-light">
                            <a href="#{{$k1}}" class="accordion__title h6 mb-0 py-3 px-4 collapsed" data-toggle="collapse" aria-expanded="true">
                                <span class="accordion__icon small mr-2 mt-1">
                                    <i class="ti-angle-down"></i>
                                    <i class="ti-angle-up"></i>
                                </span>
                                {{$k1}}
                            </a>
                            <div id="{{$k1}}" class="collapsed" data-parent="#accordion-7">
                                <div class="px-4">
                                    <ul>
                                        @foreach($methods as $k2 => $urls)
                                        <li>
                                            <h5><strong>{{$k2}}</strong></h5>
                                        </li>

                                        <ul>
                                            @foreach($urls as $k3 => $u)
                                            <li>
                                                <h6><a target="_blank" href="{{url($u)}}">{{url($u)}}</a></h6>
                                            </li>
                                            @endforeach
                                        </ul>

                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div> <!-- END accordion-item-->

                        @endforeach
                    </div> <!-- END accordion-7-->
                </div> <!-- END col-12 -->
            </div> <!-- END row-->
        </div> <!-- END container-->
    </div>
</section>

@endsection
