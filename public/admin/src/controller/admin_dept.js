/**

 @Name：layuiAdmin 部门管理
 @Author：star1029
 @Site：http://www.layui.com/admin/
 @License：LPPL

 */

layui.define(['table', 'form'], function (exports) {
    var $ = layui.$
        , admin = layui.admin
        , admin_set = layui.setter.admin_set
        , table = layui.table
        , form = layui.form;

    //数据列表
    table.render({
        elem: '#LAY-admin-dept-list-table'
        , url: admin_set.domain+'/admin/admin_dept/getData'
        , cols: [[
            {type: 'checkbox'}
            , {field: 'dept_name', title: '部门名称', minWidth: 80}
            , {field: 'pid_text', title: '上级部门', minWidth: 100}
            ,{title:'更改排序', align:'center', minWidth: 90, templet: function (data) {
                return '<div class="layui-btn-group">' +
                        '<button type="button" class="layui-btn layui-btn-primary layui-btn-xs change_sort" data-type="asc" data-id="'+ data.dept_id +'"><i class="layui-icon layui-icon-up"></i></button>' +
                        '<button type="button" class="layui-btn layui-btn-primary layui-btn-xs change_sort" data-type="desc" data-id="'+ data.dept_id + '"><i class="layui-icon layui-icon-down"></i></button>' +
                        '</div>';
            }}
            , {field: 'create_time', title: '添加日期', minWidth: 100}
            , {title: '操作', minWidth: 120, align: 'center', toolbar: '#LAY-admin-dept-list-table-option'}
        ]]
        , where: {
            access_token: layui.data(layui.setter.tableName).access_token
        }
        , text: { none: '暂无数据'}
        , done: function(res, curr, count){
            $(".change_sort").ajaxSort('/admin/admin_dept/changeSort');//改变排序
        }
    });

    //监听工具条
    table.on('tool(filter-admin-dept-list-table)', function (obj) {
        var data = obj.data;
        if (obj.event === 'del') {
            layer.confirm('确定删除吗？', function(index){
                admin.req({
                    url: '/admin/admin_dept/delete'
                    , data: {id: data.dept_id}
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
            location.hash = '/admin/dept_edit/dept_id='+data.dept_id;
        }
    });

    //搜索栏初始化。并赋值
    form.render(null, 'filter-admin-dept-list');

    //监听搜索
    form.on('submit(filter-admin-dept-list-search)', function (data) {
        var field = data.field;
        //执行重载
        table.reload('LAY-admin-dept-list-table', {
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
            var checkStatus = table.checkStatus('LAY-admin-dept-list-table')
                    , checkData = checkStatus.data; //得到选中的数据

            if (checkData.length === 0) {
                return layer.msg('请选择数据');
            }

            //获取当前选中的id，数组
            var id_list = [];
            $.each(checkData, function (key, val) {
                id_list[key] = val.dept_id;
            });

            layer.confirm('确定删除吗？', function(index){
                admin.req({
                    url : '/admin/admin_dept/delete'
                    ,data : {id:id_list}
                    ,done: function (res) {
                        layer.close(index);
                        if (res.code) {
                            table.reload('LAY-admin-dept-list-table');
                            layer.msg(res.msg);
                        } else {
                            layer.alert(res.msg, {icon:2});
                        }
                    }
                });
            });
        }
        , add: function () {
            location.hash = '/admin/dept_edit';
        }
    };

    $('#LAY-admin-dept-list-btn .layui-btn').on('click', function () {
        var type = $(this).data('type');
        active[type] ? active[type].call(this) : '';
    });

    exports('admin_dept', {})
});