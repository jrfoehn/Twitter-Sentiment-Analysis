@extends('layouts.app')



@section('content')

<div class="container-fluid">
    <div class="row main">

        <!-- sidebar -->
        <!--
        <div class="col-md-3">
            <b>Tweets</b>
            <ul id="hashtag-list"></ul>
        </div>
        -->

        <!-- main contain -->
        <div class="col-md-12">
            <div class="row">
                <div class="col-md-10">
                    <input class="form-control" type="text" id="hashtag" placeholder="ex: #Hashtag">
                </div>
                <div class="col-md-2">
                    <button class="btn btn-default" href="#" id="btnPlus" type="button" data-toggle="collapse" data-target="#collapseExample" aria-expanded="false" aria-controls="collapseExample">
                        <span class="glyphicon glyphicon-collapse-down" ></span>
                    </button>

                    <button class="btn btn-default" href="#" id="btnPlus" aria-hidden="true" data-toggle="modal" data-target="#modalInfo">
                        <span class="glyphicon glyphicon-info-sign"></span>
                    </button>
                    <button class="btn btn-info pull-right" id="btnSearch">SEARCH</button>
                </div>
            </div>

            <div class="collapse" id="collapseExample">
                <div class="well">
                    <div class="row">
                        <div class="col-md-3">
                            <label for="">Tweet number: </label>
                            <input id="nbTweets" data-slider-id='nbTweetsSlider' type="text" data-slider-min="15" data-slider-max="1000" data-slider-step="5" data-slider-value="100"/>
                        </div>
                        <div class="col-md-3">
                            <label for="">Streaming: </label>
                            <label id="streamingWrapper">
                                <input type="checkbox" name="streaming" id="streaming">
                            </label>
                            </div>
                        <div class="col-md-2">
                            <label>Result type:</label>
                            <select id="result_type">
                                <option value="mixed">Mixed</option>
                                <option value="recent">Most recent</option>
                                <option value="popular">Most popular</option>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3">
                            <span  class="input-append date" id="datetimepicker1">
                                <label>Until:</label>
                                <input id="until" type="text">
                                <span class="add-on">
                                    <span class="glyphicon glyphicon-calendar"></span>
                                </span>
                            </span>
                        </div>
                        <div class="col-md-3">
                            <label>Language:</label>
                            <select class="input-medium bfh-languages" data-language="en" id="language" data-value="en"></select>
                        </div>
                        <div class="col-md-2">
                            <label>Analyse Sentiments:</label>
                            <label id="sentimentAnalysisWrapper">
                                <input type="checkbox" name="analyse" id="analyse">
                            </label>
                        </div>
                        <div class="col-md-3">
                            <label>Nearby:</label>
                            <label id="nearbyWrapper">
                                <input type="checkbox" name="nearby" id="nearby">
                            </label>
                            <select id="radius">
                                <option value="100km">&lt; 100km</option>
                                <option value="500km">&lt; 500km</option>
                                <option value="1000km">&lt; 1000km</option>
                                <option value="2000km">&lt; 2000km</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-10">
                    <div class="graph-view">
                        <div id="cy"></div>
                    </div>
                    <div class="form-inline">
                        <button type="button" class="btn btn-info" data-toggle="modal" data-target="#modalExport" id="btnexport">EXPORT</button>
                        <button type="button" class="btn btn-info" data-toggle="modal" data-target="#modalImport" id="btnImport">IMPORT</button>
                        <div id="filterContainer">
                            <label>Number minimum of weight to display:</label>
                            <input type="text" class="form-control" id="nbWeight" placeholder="1">
                            <button type="button" class="btn btn-info" id="btnFilter">Filter</button>
                        </div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="panel panel-default hashtags-view">
                        <div class="panel-heading">Hashtag list</div>
                        <div class="panel-body">
                            <div>Sort:</div>
                            <div class="row">
                                <div class="col-md-6">
                                    <button id="btnSortLabel" class="btn btn-default btn-xs btn-sort btn-order-1">
                                        <span class="">Label</span>
                                        &nbsp;
                                        <span class="glyphicon glyphicon-chevron-up icon-order-0"></span>
                                        <span class="glyphicon glyphicon-chevron-down icon-order-1"></span>
                                    </button>
                                </div>
                                <div class="col-md-6">
                                    <button id="btnSortWeight" class="btn btn-default btn-xs btn-sort btn-order-1">
                                        <span class="">Weight</span>
                                        &nbsp;
                                        <span class="glyphicon glyphicon-chevron-up icon-order-0"></span>
                                        <span class="glyphicon glyphicon-chevron-down icon-order-1"></span>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <ul id="hashtag-list" class="list-group"></ul>
                    </div>
                </div>
            </div>

            <!-- * BEGIN Modals * -->
            <div class="modal fade" id="modalImport" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title" id="myModalLabel">Choose files to import</h4>
                        </div>
                        <div class="modal-body">
                            <input type="file" id="file-select" name="files[]" multiple/>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                            <button type="button" class="btn btn-primary" id="btnUpload">Import</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="modalInfo" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title" id="myModalLabel">How to build a complex query:</h4>
                        </div>
                        <div class="modal-body">
                            <table class="table table-condensed">
                                <thead>
                                <tr>
                                    <th>Operator</th>
                                    <th>Signification</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr>
                                    <td>#hashtag</td>
                                    <td>containing the hashtag "hashtag"</td>
                                </tr>
                                <tr>
                                    <td>#hashtag -RT</td>
                                    <td>containing “hashtag” exclude retweets.</td>
                                </tr>
                                <tr>
                                    <td>#hashtag1 #hashtag2</td>
                                    <td>containing both hashtags hashtag1 and hashtag2.</td>
                                </tr>
                                <tr>
                                    <td>#hashtag1 OR #hashtag2</td>
                                    <td>containing either hashtag hashtag1 or hashtag2 (or both).</td>
                                </tr>
                                <tr>
                                    <td>#hashtag1 -#hashtag2</td>
                                    <td>containing “hashtag1” but not “hashtag2”.</td>
                                </tr>
                                </tbody>
                            </table>
                            <p>For more information, see
                                <a href="https://dev.twitter.com/rest/public/search">The Twitter's Search API</a>
                            </p>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="modalCredit" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title" id="myModalLabel">Credits</h4>
                        </div>
                        <div class="modal-body">
                            <p>This project is conceived and implemented in version alpha by Yihe WANG,  supervised by Babiga BIRREGAH.</p>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="modalExport" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title" id="myModalLabel">Image export</h4>
                        </div>
                        <div class="modal-body">
                            <div class="alert alert-info"><em>Right click and save the image below</em></div>
                            <img id="graph-export" src="">
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </div>
            <!-- * END Modals * -->

            <!-- * BEGIN Popover * -->
            <div class="popover fade right in">
                <h3 class="popover-title">
                    <span class="popover-title-text"></span>
                    <button type="button" class="close" data-dismiss="popover" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </h3>
                <div class="popover-content"></div>
            </div>
            <!-- * END Popover * -->

        </div>
    </div>
</div>

<script type="text/javascript" src="{!! asset('js/bootstrap-formhelpers-languages.js') !!}"></script>
<script type="text/javascript">
var startDate = new Date();
    var endDate = new Date();
    startDate.setDate(startDate.getDate() - 8);
    $(function () {
        $("#datetimepicker1 > :input").datetimepicker({
            format: 'YY-MM-DD',
            minDate: startDate,
            maxDate: endDate
        });
        $('#language').bfhlanguages();
    });
</script>

@endsection