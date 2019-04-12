/**

 @Name：layuiAdmin 菜单管理
 @Author：star1029
 @Site：http://www.layui.com/admin/
 @License：LPPL

 */

layui.define(['table', 'form'], function (exports) {
    var $ = layui.$
        , admin = layui.admin
        , admin_set = layui.setter.admin_set
        , table = layui.table
        , view = layui.view
        , element = layui.element
        , form = layui.form;

    //数据列表
    table.render({
        elem: '#LAY-set-menu-list-table'
        , url: admin_set.domain+'/admin/menu/getMenuList'
        , cols: [[
            {type: 'checkbox'}
            , {field: 'menu_name', title: '菜单名称', minWidth: 180}
            , {field: 'jump', title: '路由地址', minWidth: 150}
            , {field: 'url', title: '权限规则', minWidth: 100}
            ,{field:'display_text', title:'是否显示', align:'center', width: 90}
            ,{field:'open_type_text', title:'打开方式', width: 95}
            ,{title:'更改排序', align:'center', width: 90, templet: function (data) {
                return '<div class="layui-btn-group">' +
                        '<button type="button" class="layui-btn layui-btn-primary layui-btn-xs" lay-event="change_sort" data-type="asc"><i class="layui-icon layui-icon-up"></i></button>' +
                        '<button type="button" class="layui-btn layui-btn-primary layui-btn-xs" lay-event="change_sort" data-type="desc"><i class="layui-icon layui-icon-down"></i></button>' +
                    '</div>';
            }}
            , {field: 'description', title: '描述', minWidth: 100}
            , {title: '操作', minWidth: 120, align: 'center', toolbar: '#LAY-set-menu-list-table-option'}
        ]]
        , height: 'full-180'
        , text: { none: '暂无数据'}
        , where: {
            access_token: layui.data(layui.setter.tableName).access_token
        }
    });

    //监听工具条
    table.on('tool(filter-set-menu-list-table)', function (obj) {
        var data = obj.data;
        if (obj.event === 'del') {
            layer.confirm('确定删除吗？', function(index){
                admin.req({
                    url: '/admin/menu/delete'
                    , data: {id: data.menu_id}
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
            location.hash = '/set/menu_edit/menu_id='+data.menu_id;
        } else if (obj.event === 'change_sort') {
            var data_attr = $(this).data();
            admin.req({
                url : '/admin/menu/changeSort'
                ,data : {id:data.menu_id,'type':data_attr.type}
                ,done: function (res) {
                    if (res.code) {
                        table.reload('LAY-set-menu-list-table');
                        view('LAY-system-side-menu').render('set/menu_tpl').done(function(){
                            element.render('nav', 'layadmin-system-side-menu');
                        });
                        layer.msg('更改成功！');
                    } else {
                        layer.alert(res.msg, {icon:2});
                    }
                }
            });
        }
    });

    //搜索栏初始化。并赋值
    form.render(null, 'filter-set-menu-list');

    //监听搜索
    form.on('submit(filter-set-menu-list-search)', function (data) {
        var field = data.field;
        //执行重载
        table.reload('LAY-set-menu-list-table', {
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
            var checkStatus = table.checkStatus('LAY-set-menu-list-table')
                    , checkData = checkStatus.data; //得到选中的数据

            if (checkData.length === 0) {
                return layer.msg('请选择数据');
            }

            //获取当前选中的id，数组
            var id_list = [];
            $.each(checkData, function (key, val) {
                id_list[key] = val.menu_id;
            });

            layer.confirm('确定删除吗？', function(index){
                admin.req({
                    url : '/admin/menu/delete'
                    ,data : {id:id_list}
                    ,done: function (res) {
                        layer.close(index);
                        if (res.code) {
                            table.reload('LAY-set-menu-list-table');
                            layer.msg(res.msg);
                        } else {
                            layer.alert(res.msg, {icon:2});
                        }
                    }
                });
            });
        }
        , add: function () {
            location.hash = '/set/menu_edit';
        }
    };

    $('#LAY-set-menu-list-btn .layui-btn').on('click', function () {
        var type = $(this).data('type');
        active[type] ? active[type].call(this) : '';
    });

    exports('menu', {})
});