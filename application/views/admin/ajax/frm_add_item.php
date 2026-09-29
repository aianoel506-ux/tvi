<?php

?>
<form id="frm_submit_items" action="<?php echo base_url(); ?>query/additems" method="post">
    <div class="modal-body">
        <div class="row">

            <div class="col-md-5">
                <div class="form-group">
                    <label for="item_select">Category
                        <a class="" href="javascript:;" data-container="body" data-toggle="popover" data-trigger="hover" data-placement="top" data-title="Category Example" data-content="Ex: Personal Computer, Transformers, Meter"><i class="fa fa-question"></i></a>
                    </label>
                    <div class="input-icon right">
                        <input class="form-control input-reset" id="item_category_search" placeholder="Search Category.." required name="category" />
                        <i class="fa fa-search"></i>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="form-group">
                    <label for="item_select">Components
                        <a class="" href="javascript:;" data-container="body" data-toggle="popover" data-trigger="hover" data-placement="top" data-title="Components Example" data-content="Ex: Keyboard, Mouse, etc."><i class="fa fa-question"></i></a>
                    </label>
                    <div class="input-icon right">
                        <input class="form-control input-reset" id="item_comp_search" placeholder="Components" required name="components" />
                        <i class="fa fa-search"></i>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="form-group">
                    <label for="item_select">Unit</label>
                    <div class="input-icon">
                        <i class="fa fa-tag"></i>
                        <input class="form-control input-reset" id="item_unit" placeholder="Unit" required name="unit" />
                    </div>
                </div>
            </div>

        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <label for="item_select">Specification
                        <a class="" href="javascript:;" data-container="body" data-toggle="popover" data-trigger="hover" data-placement="top" data-title="Specification Example" data-content="Querty Keyboard Mechanical w/ Num Pads, Gaming Mouse RoG."><i class="fa fa-question"></i></a>
                    </label>
                    <div class="input-icon">
                        <i class="fa fa-tag"></i>
                        <input class="form-control input-reset" id="item_spec_search" placeholder="Specification" required name="specifications" />
                    </div>
                </div>
            </div>
        <hr>
    </div>
    <div class="modal-footer">
        <button type="reset" class="btn btn-danger"><i class="fa fa-refresh"></i> Reset</button>
        <button type="submit" class="btn btn-primary"><i class="fa fa-save"></i> Save</button>
    </div>
</form>

<script>
    PECO.select2Basic($('#item_unit', document), 'query/getunits', 'Unit...');
    PECO.handlerSearchItemCategory();

    var ITEMADD = function() {
        var add_item_fn = function() {
            $('[data-toggle="popover"]').popover({
                html: true,
                animated: true,
            });
        };
        return {
            init: function() {
                add_item_fn();
            }
        }
    }();

    ITEMADD.init();
</script>