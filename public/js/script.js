/**
 * Created by jrfoehn on 10/28/16.
 */
$(document).ready(function(){

  var rootURL = "http://www.hashtag.dev/api/twitter/";

  var G_DATAGRAPH;
  var G_cy;
  var LATITUDE;
  var LONGITUDE;

  $('#nbTweets').slider({
    formatter: function(value) {
      return 'Current value: ' + value;
    }
  });

  $('#streaming').bootstrapSwitch({
    size: 'mini'
  });

  $('#nearby').bootstrapSwitch({
    size: 'mini'
  });


  $('#radius').prop('disabled', true);

  $('#nearby').on('switchChange.bootstrapSwitch', function (e, state) {
    if (state) {
      if("geolocation" in navigator) {
        $('#radius').prop('disabled', false);
        navigator.geolocation.getCurrentPosition(function(position) {
          LATITUDE = position['coords']['latitude'];
          LONGITUDE = position['coords']['longitude'];
        });
      }else {
        alert(" Your browser doesn't support geolocation");
      }
    }else{
      $('#radius').prop('disabled', true);
      LATITUDE = null;
      LONGITUDE = null;
    };
  });

  $('#btnLogout').on('click', function(){
    var r = confirm("Are you sure you want to log out?");
    if (r === true) {
      window.location.href="login.php";
    };
  });

  $('#btnSearch').on('click', function(){
    $("#filterContainer").empty();
    searchHashtag();
    return false;
  });

  $('#btnFilter').on('click', function(){
    var minWeight = $('#nbWeight').val();
    $.ajax({
      type:'POST',
      url: rootURL + 'filter',
      data:{
        'media': $('#media').val()
      },
      dataType: 'json',
      success: function(data){
        buildHashtagList(data);
        G_DATAGRAPH = data;
        buildGraph(filterEdges(G_DATAGRAPH, minWeight));
      }
    });
  })

  $('#btnexport').on('click', function(){
    exportFile();
  });

  $('#btnUpload').on('click', function(){
    var fileSelect = $('#file-select');
    var uploadButton = $('#btnUpload');

    // Fetch selected files and drop them into FormData object for upload
    var files = fileSelect[0].files;
    var formData = new FormData();
    for (var i = 0; i < files.length; i++) {
      var file = files[i];
      formData.append('file[]', file, file.name);
    }

    // UI changes
    uploadButton.html('Uploading...');
    uploadButton.prop('disabled', true);
    fileSelect.prop('disabled', true);

    // Upload to server
    $.ajax({
      type:'POST',
      url: rootURL + 'import',
      dataType: 'json',
      data: formData,
      //Options to tell jQuery not to process data or worry about content-type.
      cache: false,
      contentType: false,
      processData: false,
      success: function(data){
        G_DATAGRAPH = data;
        buildGraph(data);
        buildHashtagList(data);

        // UI changes back
        uploadButton.html('Import');
        uploadButton.prop('disabled', false);
        fileSelect.prop('disabled', false);
        // Reset file input
        fileSelect.wrap('<form>').closest('form').get(0).reset();
        fileSelect.unwrap();
        // Fade out modal
        $('#modalImport').modal('hide');
      }
    });
  });

//Button for sorting
  $('#btnSortLabel').on('click', function() {
    buttonSortAction('alpha', $(this));
  });
  $('#btnSortWeight').on('click', function() {
    buttonSortAction('weight', $(this));
  });

//Enter to submit
  $('#hashtag').on('keydown', function(e){
    if(e.keyCode == 13){
      searchHashtag();
    }
  });
  $('#nbWeight').on('keydown', function(e){
    if(e.keyCode == 13){
      var minWeight = $(this).val();
      buildGraph(filterEdges(G_DATAGRAPH, minWeight));
    }
  });

//search for a hashtag
  function searchHashtag(){
    $('#hashtag').prop('readonly', true);
    $('#btnSearch').prop('disabled', true).html("Loading");
    if (LONGITUDE&&LATITUDE) {
      var geocode = LATITUDE.toString() + "," + LONGITUDE.toString() + "," + $('#radius').val();
    } else{
      var geocode = "";
    };
    if ($('#analyse').prop('checked')) {
      var analyse = 'true';
    }
    else {
      var analyse = 'false'
    }
    $.ajax({
      type:'POST',
      url: rootURL + 'search',
      dataType: 'json',
      data: {
        'hashtag': $('#hashtag').val(),
        'cnt': $('#nbTweets').val(),
        'streaming': $('#streaming').prop('checked'),
        'result_type': $('#result_type').val(),
        'until': $('#until').val(),
        'lang': $('#language').val(),
        'geocode': geocode,
        'analyse' : analyse
      },
      success: function(data){
        var results = data.nodes;
        var color ='#31b0d5';
        for (i =0; i < results.length; i++) {
          var sentiment = results[i].data.sentiment;
          if (sentiment < -0.3) {
            color = '#ff0000';
          }
          else if (sentiment > 0.3) {
            color = '#008000';
          }
          else {
            color = '#31b0d5';
          }
          results[i].data.color = color;
        }
        console.log(data);
        buildGraph(data);
        buildHashtagList(data);
        // console.log(results);
        G_DATAGRAPH = data;
        $('#hashtag').prop('readonly', false);
        $('#btnSearch').prop('disabled', false).html("SEARCH");
      }
    });
  }

  function exportFile(){
    $.ajax({
      type: 'GET',
      url: rootURL + 'export',
      success: function(data) {
        //download automatical
        $('body').append('<iframe width="1" height="1" frameborder="0" src="' + data + '"></iframe>');
        //$('body').append('<iframe width="1" height="1" frameborder="0" src="' + G_cy.png() + '"></iframe>');
        $('#graph-export').attr('src', G_cy.png());
      }
    });
  }

//Filter edges
  function filterEdges(graph, minWeight) {
    var newGraph = jQuery.extend(true, {}, graph);

    newGraph.edges = newGraph.edges.filter(function(edge){
      if (edge.data.weight < minWeight) {
        var source = newGraph.nodes.filter(function(node){
          return node.data.id === edge.data.source;
        })[0];
        var target = newGraph.nodes.filter(function(node){
          return node.data.id === edge.data.target;
        })[0];
        source.data.weight -= edge.data.weight;
        target.data.weight -= edge.data.weight;
        return false;
      } else {
        return true;
      }
    });

    newGraph.nodes = newGraph.nodes.filter(function(node){
      return node.data.weight > 0;
    });

    return newGraph;
  }

  function buildGraph(graph) {
    G_cy = cytoscape({
      container: $('#cy')[0],


      style: cytoscape.stylesheet()
          .selector('node')
          .css({
            'content': 'data(label)',
            'text-valign': 'center',
            'color': 'white',
            'text-outline-width': 2,
            'text-outline-color': 'data(color)',
            'height': 50,
            'width': 50,
            'background-color': 'data(color)'
          })
          .selector('edge')
          .css({
            'content': 'data(weight)',
            'width': 4,
            'curve-style': 'haystack',
            'haystack-radius': 0,
            'opacity': 0.8,
            'line-color': '#a8eae5'

          })
          .selector(':selected')
          .css({
            'background-color': '#006666',
            'line-color': '#006666',
            'target-arrow-color': '#006666',
            'source-arrow-color': '#006666',
            'text-outline-color': '#006666'
          }),

      elements: graph,

      layout: {
        name: 'concentric'
      }
    });

    G_cy.on('click', 'edge', function(e){
      var tweet_contents = '';
      var tweets = e.cyTarget.data('tweets');

      for (var i = 0; i < tweets.length; i++) {
        tweet_contents += '<li>' + tweets[i] + '</li>';
      }

      // show popover with tweets referenced to clicked edge
      // set title by the relation of 2 hashtags
      $('.popover .popover-title .popover-title-text').html(
          e.cyTarget.data('source') + ' - ' + e.cyTarget.data('target')
      );
      // set content by the linked tweets of the relation
      $('.popover .popover-content').html(tweet_contents);
      // show popover at the position of click
      $('.popover')
          .css('left', e.cyRenderedPosition.x)
          .css('top', e.cyRenderedPosition.y - 15)
          .show();

      // dismiss popover when clicking on 'x'
      $('.popover .popover-title .close').on('click', function() {
        $('.popover').hide();
      });
    });

    //display filter container
    $('#filterContainer').show();
    $('#btnexport').show();
  }

  function buildHashtagList(graph) {
    var nodesCpy = graph.nodes.slice();
    // Default sorted by descending weight
    nodesCpy = sortHashtags('weight', 1, nodesCpy);
    displayHashtagList(nodesCpy);
  }

  function displayHashtagList(hashtagList) {
    var hashtags = '';
    hashtagList.forEach(function(e) {
      var id = e.data.id;
      var title = '#' + id;
      var weight = e.data.weight;
      hashtags += '<li id="h_' + id + '" class="list-group-item">' +
          '<span class="badge">' + weight + '</span>' +
          title +
          '</li>';
    });
    $('#hashtag-list').html(hashtags);
  }

// criterion in ['alpha', 'weight']:
//   'alpha' for sorting by alphabet
//   'weight' for sorting by weight
// order in [0, 1]:
//   0 for ascending order
//   1 for descending
  function sortHashtags(criterion, order, hashtags) {
    var compare;
    if (criterion === 'alpha') {
      compare = function(h1, h2) {
        if (order === 0) {
          return h1.data.id.localeCompare(h2.data.id);
        } else {
          return h2.data.id.localeCompare(h1.data.id);
        }
      };
    } else {
      compare = function(h1, h2) {
        if (order === 0) {
          return h1.data.weight - h2.data.weight;
        } else {
          return h2.data.weight - h1.data.weight;
        }
      };
    }

    hashtags.sort(compare);
    return hashtags;
  }

  function buttonSortAction(criterion, dom) {
    if (G_DATAGRAPH === undefined) {
      return false;
    }

    var nodesCpy = G_DATAGRAPH.nodes.slice();
    if (dom.hasClass('btn-order-1')) {
      nodesCpy = sortHashtags(criterion, 0, nodesCpy);
      dom.removeClass('btn-order-1').addClass('btn-order-0');
    } else {
      nodesCpy = sortHashtags(criterion, 1, nodesCpy);
      dom.removeClass('btn-order-0').addClass('btn-order-1');
    }

    displayHashtagList(nodesCpy);
  }

});
//# sourceMappingURL=script.js.map

//# sourceMappingURL=script.js.map
