
                                    @extends('layouts.site')

                                    @section('content')

                                        <div class="row justify-content-center">
                                            <div class="col-md-8">
                                                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                                    @csrf
                                                </form>
                                            </div>
                                        </div>


