/**

 @Name：layuiAdmin 用户管理 管理员管理 角色管理
 @Author：star1029
 @Site：http://www.layui.com/admin/
 @License：LPPL

 */

layui.define(['table', 'form'], function (exports) {
    var $ = layui.$
        , admin = layui.admin
        , table = layui.table
        , form = layui.form;


    //监听工具条
    table.on('tool(filter-photo-list-table)', function (obj) {
        var data = obj.data;
        if (obj.event === 'del') {
            layer.confirm('确定删除吗？', function(index){
                admin.req({
                    url: '/admin/photo/delete'
                    , data: {id: data.photo_id}
                    , done: function (res) {
                        layer.close(index);
                        if (res.code) {
                            obj.del();
                            layer.msg(res.msg);
                        } else {
                            layer.alert(res.msg, {icon:2});
                        }
                    }
                });
            });
        } else if (obj.event === 'edit') {
            location.hash = '/photo/edit/photo_id='+data.photo_id;
        }
    });

    //搜索栏初始化。并赋值
    form.render(null, 'filter-photo-list');

    //监听搜索
    form.on('submit(filter-photo-list-search)', function (data) {
        var field = data.field;
        //执行重载
        table.reload('LAY-photo-list-table', {
            where: field
            ,page:{
                curr: 1 //重新从第 1 页开始
            }
        });
        return false;
    });

    //事件
    var active = {
        batchdel: function () {
            var checkStatus = table.checkStatus('LAY-photo-list-table')
                    , checkData = checkStatus.data; //得到选中的数据

            if (checkData.length === 0) {
                return layer.msg('请选择数据');
            }

            //获取当前选中的id，数组
            var id_list = [];
            $.each(checkData, function (key, val) {
                id_list[key] = val.photo_id;
            });

            layer.confirm('确定删除吗？', function(index){
                admin.req({
                    url : '/admin/photo/delete'
                    ,data : {id:id_list}
                    ,done: function (res) {
                        layer.close(index);
                        if (res.code) {
                            table.reload('LAY-photo-list-table');
                            layer.msg(res.msg);
                        } else {
                            layer.alert(res.msg, {icon:2});
                        }
                    }
                });
            });
        }
        , add: function () {
            location.hash = '/photo/edit';
        }
    };

    $('#LAY-photo-list-btn .layui-btn').on('click', function () {
        var type = $(this).data('type');
        active[type] ? active[type].call(this) : '';
    });

    exports('photo', {})
});