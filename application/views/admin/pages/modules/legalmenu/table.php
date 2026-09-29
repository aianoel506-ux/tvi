<div class="portlet light bordered table">
    <div class="portlet-title">
        <div class="caption">
            <span class="caption-subject bold"><i class="icon-refresh font-green-sharp"></i> Sync From Legacy</span>
        </div>
        <div class="tools tabbable-line">
            <ul class="nav nav-tabs " id="tab_types">
                <li class="active" data-id="1">
                    <a href="#ra" role="tab" data-toggle="tab"><i class="fa fa-deafness bold"></i> RA7832</a>
                </li>
                <li class="" data-id="2" >
                    <a href="#bp" role="tab"data-toggle="tab"><i class="fa fa-scissors bold"></i> BP22</a>
                </li>
            </ul>
        </div>
    </div>
    <div class="portlet-body">
        <table class="table table-hover table-bordered tbl-sm " id="tbl_legal_sync">
            <thead>
            <th>#</th>
            <th>Servno</th>
            <th>Name</th>
            <th>Address</th>
            <th>Legacy Amount</th>
            <th>ERP Amount</th>
            <th>Status</th>
            <th>Synced</th>
            <th>Control</th>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</div>

<script type="text/javascript">

    var LEGALMAIN = function() {
        var tbl_legal_sync = $('#tbl_legal_sync', document);
        var init_fn = function() {
            init_tbl_ra(1);
            $('#tab_types', document).on('click', 'li', function() {
                var this_id = $(this).attr('data-id');
                init_tbl_ra(this_id);
            });
        };

        var init_tbl_ra = function(typesid) {
            $.ajax({
                url: PECO.base_url() + 'legal/getdtsync',
                type: 'post',
                data: {'types': typesid},
                dataType: 'json',
                beforeSend: function() {
                    PECO.DTphpLoading(tbl_legal_sync, 'Loading sync data...');
                }
            }).done(function(d) {
                tbl_legal_sync.DataTable({
                    bDestroy: true,
                    bPaginate: true,
                    bFilter: true,
                    bInfo: true,
                    bStateSave: true,
                    bProcessing: true,
                    pageLength: 25,
                    aaData: d.list,
                    aoColumns: [
                        {data: 'num', sClass: '', sWidth: ''},
                        {data: 'servno', sClass: '', sWidth: ''},
                        {data: 'name', sClass: '', sWidth: ''},
                        {data: 'address', sClass: '', sWidth: ''},
                        {data: 'bal', sClass: 'number text-danger', sWidth: '10%'},
                        {data: 'erpamt', sClass: 'number text-info', sWidth: '10%'},
                        {data: 'status', sClass: '', sWidth: ''},
                        {data: 'num', sClass: '', sWidth: ''},
                        {data: 'num', sClass: '', sWidth: ''},
                    ],
                    fnRowCallback: function(nRow, aData, i) {
                        $(nRow).addClass(aData.rowclass);
                    }
                });
            }).fail(function() {

            });
        };

        return {
            init: function() {
                init_fn();
            }
        }
    }();

    LEGALMAIN.init();

</script>