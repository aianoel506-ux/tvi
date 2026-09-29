<?php
$ids = $this->input->post('ids');
$ids_arr = explode(',', $ids);

if(is_array($ids_arr) && count($ids_arr)>1) {
    $dataid = $ids_arr[0];
    $moduleid = $ids_arr[1];
?>

    <form id="frm_add_team_member" method="post" action="<?php echo base_url('inspection/addteammember');?>">

        <div class="modal-body">
                <label class="control-label">Search name</label>
                <input class="form-control" type="hidden" id="empid" name="empid" />
                <input class="form-control" type="hidden" name="dataid" value="<?php echo $dataid; ?>" />
                <input class="form-control" type="hidden" name="moduleid" value="<?php echo $moduleid; ?>"  />
                <div class="form-group" style="margin-bottom: 30px; display: inline-block; width: 100%;">
                    <div class="input-icon right" style="width: 100%;">
                        <input class="form-control" placeholder="Lastname..." id="lastname" />
                        <i class="fa fa-search"></i>
                    </div>
                </div>
                <div class="form-group" style="margin-top: 20px;">
                    <ul class="list-group summary column no-border margin-top-20">
                        <li class="list-group-item">
                            <span class="col-md-5 label-name">Firstname</span>
                            <span class="col-md-7 label-default" id="text_firstname"></span>
                        </li>
                        <li class="list-group-item">
                            <span class="col-md-5 label-name">Middlename</span>
                            <span class="col-md-7 label-default" id="text_middlename"></span>
                        </li>
                    </ul>
                </div>
        </div>
        <div class="modal-footer">
            <button class="btn btn-primary">Add</button>
        </div>
    </form>


    <script>

        var empid = $('#empid', document);
        var lastname = $('#lastname', document);
        var firstname = $('#text_firstname', document);
        var middlename = $('#text_middlename', document);

        var a = new Bloodhound({
            datumTokenizer: function (e) {
                return e.tokens
            },
            queryTokenizer: Bloodhound.tokenizers.whitespace,
            remote: {url: PECO.base_url() + "search/employeesearch?query=%QUERY", wildcard: "%QUERY"}
        });

        a.initialize(), lastname.typeahead(null, {
            hint: false,
            highlight: true,
            minLength: 1,
            displayKey: "lastname",
            source: a.ttAdapter(),
            cache: false,
            templates: {
                suggestion: Handlebars.compile(['<div class="media">', '<div class="pull-left">', '<div class="media-object">', '<img src="{{img}}" width="50" height="50"/>', "</div>", "</div>", '<div class="media-body">', '<h5 class="media-heading text-primary"><b class="text-glow-yellow">{{lastname}}</b>, {{firstname}} {{middlename}}</h5>', "<p>{{district}} - {{addr}}</p>", "</div>", "</div>"].join("")),
            },
        }).on('typeahead:selected', function(event, selection) {
            empid.val(selection.empid);
            firstname.text(selection.firstname);
            middlename.text(selection.middlename);
        }).click(function() {
            PECO.initElScroller($('.tt-dropdown-menu', document));
        });


    </script>
<?php
}else{
    page_data_notfound_modal('Sent ids not valid!');
}
?>

