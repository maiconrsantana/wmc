jQuery(function($){

    class cls_grantt{
        constructor(container){
            this.rooms = [];
            this.container = container;

            var start = moment().format();
            var end = moment().add(60, 'days').format();

            this.init(start, end);
        }
        init(start, end){
            console.log(end);
            var self = this;
            $(self.container).each(function(){
                var t = $(this);
                t.gantt({
                    source: self.rooms,
                    navigate: "scroll",
                    maxScale: "hours",
                    itemsPerPage: 20,
                    dateStart: start,
                    dateEnd: end,
                    onItemClick: function(data) {

                    },
                    onAddClick: function(dt, rowId) {

                    },
                    onRender: function() {

                    },
                    onDataLoadFailed: function(data) {
                        alert("Data failed to load!")
                    }
                });
            });
        }

        setRoom(_rooms){
            this.rooms = [];
            this.rooms = _rooms;
        }
    }
    var _grantt;
    $('body').on('click', 'a[href="#setting_inventory_tab"]', function(event){
        _grantt = new cls_grantt('.gantt');
        var parent = $('.change-date-inventory');
        var spinner = $('.spinner', parent);
        var start = moment().format("YYYY-MM-DD");
        var end = moment().add(60, 'days').format("YYYY-MM-DD");
        spinner.show();
        //=== featch data before reinit grantt
        var data = {
            'action' : 'st_fetch_inventory',
            'start' : moment(start).format("YYYY-MM-DD"),
            'end': moment(end).format("YYYY-MM-DD"),
            'post_id': parent.data('post-id')
        };
        $.post(ajaxurl, data, function(respon){
            if(typeof respon === 'object'){
                _grantt.setRoom(respon.rooms);
                _grantt.init(start, end);
            }
            spinner.hide();
        }, 'json');
    });

    $('.change-date-inventory button').click(function(event){
        var t = $(this),
            parent = t.closest('.change-date-inventory');

        var month = $('select[name="change_month_inventory"]', parent).val();
        var year = $('select[name="change_year_inventory"]', parent).val();
        var spinner = $('.spinner', parent);

        if(typeof _grantt != 'undefined'){
            var start = moment("" + year + "-" + month + "-01").format("YYYY-MM-DD");
            var end = moment("" + year + "-" + month + "-01").add(60, 'days').format("YYYY-MM-DD");
            spinner.show();
            //=== featch data before reinit grantt
            var data = {
                'action' : 'st_fetch_inventory',
                'start' : moment(start).format("YYYY-MM-DD"),
                'end': moment(end).format("YYYY-MM-DD"),
                'post_id': parent.data('post-id')
            };
            $.post(ajaxurl, data, function(respon){
                if(typeof respon === 'object'){
                    _grantt.setRoom(respon.rooms);
                    _grantt.init(start, end);
                }
                spinner.hide();
            }, 'json');
        }

        event.preventDefault();
    });
});

