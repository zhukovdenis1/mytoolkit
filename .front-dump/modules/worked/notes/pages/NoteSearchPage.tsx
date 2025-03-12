import React, { useEffect, useState } from "react";
import { Link, useNavigate, useLocation } from "react-router-dom";

import {Table, Input, Button, Form, Space, Select, showModal, Confirmable, message, TreeSelect} from "ui";
import type { TablePaginationConfig, ColumnsType } from "@ui/types";
import {api, route, log} from "api"
import {NoteFormPage} from "./NoteFormPage";
import {convertTreeData} from "@/utils/ui";

interface DataType {
    id: number;
    title: string;
}

let prevPage = 0;

export const NoteSearchPage: React.FC = () => {

    const navigate = useNavigate();
    const location = useLocation();
    const [form] = Form.useForm();
    const [data, setData] = useState<DataType[]>([]);
    const [categoriesTree, setCategoriesTree] = useState([]);
    const [loading, setLoading] = useState(false);
    const [pagination, setPagination] = useState<TablePaginationConfig>({
        current: 1,
        pageSize: 10,
        total: 0,
        showSizeChanger: true,
        onChange: () => {},
    });
    const [sorter, setSorter] = useState<{ field?: string; order?: string }>({});
    // Колонки таблицы
    const columns: ColumnsType<DataType> = [
        { title: "ID", dataIndex: "id", key: "id", sorter: true, width: 50 },
        {
            title: "Name",
            dataIndex: "title",
            key: "title",
            sorter: true ,
            render: (_, record) =>
                <>
                    <Link to={route('notes.view', {note_id: record.id})}>{record.title}</Link>
                </>
        },
        {
            title: "Actions",
            key: "action",
            fixed: "right",
            width: 200,
            //render: (_, record) => <Link to={route('notes.edit', {note_id: record.id})}>Edit</Link>,
            render: (_, record) =>
                <>
                    <Button type="link" onClick={() => showModal(<NoteFormPage />,  {
                        title: "Edit note",
                        loading: true,
                        styleName: 'wide',
                        data: {id: record.id},
                        onClose: (data) => {
                            if (data?.reload) {
                                reload();
                            }
                        }
                    })}>edit</Button>
                    <Confirmable onConfirm={() => {deleteNote(record.id)}}>
                        <Button type="link">delete</Button>
                    </Confirmable>
                </>
        },
    ];

    // Обработчик изменения страницы
    useEffect(() => {
        const params = Object.fromEntries(new URLSearchParams(location.search).entries());
        //form.setFieldsValue({ search: params.search, categories: params.categories });
        form.setFieldsValue({
            search: params.search,
            categories: params.categories ? params.categories.split(',') : []
        });
        setPagination((prev) => ({
            ...prev,
            current: Number(params._page) || 1,
            pageSize: Number(params._limit) || 5,
            total: Number(params._total) || 0,
        } as TablePaginationConfig));//чтобы phpstorm не ругался
        fetchData(params);
    }, [location.search]);


    // Функция для обновления строки навигации
    const updateUrl = (params: any) => {
        const queryString = new URLSearchParams(params).toString();
        navigate(`?${queryString}`);
        //navigate(route('notes.search', {search: values.search,  _page: page, _limit: pageSize}));
    };

    // Функция загрузки данных
    const fetchData= async (params = {}) => {
        setLoading(true);

        if (params.categories && typeof params.categories === "string") {
            params.categories = params.categories.split(',');
        }
        const response = await api.safeRequest("notes.search", params);
        setData(response?.data?.data);
        setPagination((prev) => ({
            ...prev,
            current: response?.data.meta.current_page, // Обновляем текущую страницу
            pageSize: response?.data.meta.per_page, // Обновляем размер страницы
            total: response?.data.meta.total, // Устанавливаем общее количество записей
        } as TablePaginationConfig));//чтобы phpstorm не ругался

        setLoading(false);
        const categoriesTreeResponse = await api.safeRequest("notes.categories.tree");
        setCategoriesTree(convertTreeData(categoriesTreeResponse?.data?.data, {id: 'value', name: 'title'}));
    };

    // Обработчик отправки формы поиска
    const handleSearch = (values: any) => {

        //alert(pagination.current +'=' + currentPage);
        let page = pagination.current;
        if (prevPage == page) {
            page = 1;
            setPagination((prev) => ({ ...prev, current: page })); //Сбрасываем текущую страницу
        }
        prevPage = pagination.current;

        const queryParams = {
            search: values.search || "",
            //categories: values.categories || [],
            categories: values.categories ? values.categories.join(',') : "",
            //_page: pagination.current,
            _page: page,
            _limit: pagination.pageSize,
            _sort: sorter.field || "",
            _order: sorter.order || "",
        };

        //setPagination((prev) => ({ ...prev, current: 1 })); //Сбрасываем текущую страницу
        updateUrl(queryParams);
        fetchData(queryParams);
        //form.submit();
    };

    const reset = () => {
        navigate(route('notes.search'));
    }

    const reload = () => {
        form.submit();
    }

    const deleteNote = async (noteId) => {
        const response = await api.safeRequest(`notes.delete`, {note_id: noteId});
        if (response?.data?.success) {
            reload();
            message.success('Note was deleted successfully')
        } else {
            message.error('Deleting failed');
        }
    }

    // Обработчик смены пагинации и сортировки
    const handleTableChange = (pag: TablePaginationConfig, _: any, sorterObj: any) => {

        const newSorter = sorterObj.order
            ? { field: sorterObj.field, order: sorterObj.order === "ascend" ? "asc" : "desc" }
            : {};

        prevPage = pagination.current;

        setPagination(pag);

        setSorter(newSorter);

        form.submit();
    };

    return (
        <div>
            <Form form={form} layout="inline" onFinish={handleSearch} className="line">
                <Form.Item name="search">
                    <Input placeholder="Search..." autoComplete="off" />
                </Form.Item>
                <Form.Item name='categories'>
                    <TreeSelect
                        disabled={loading}
                        showSearch
                        //style={{ width: '300px' }}
                        dropdownStyle={{ minWidth: 200,  maxHeight: 400, overflow: 'auto' }}
                        placeholder="Please select"
                        allowClear
                        multiple
                        treeDefaultExpandAll
                        treeData={categoriesTree}
                        filterTreeNode={(input, node) =>
                            node.title.toLowerCase().includes(input.toLowerCase())
                        }
                    />
                </Form.Item>
                <Form.Item>
                    <Space>
                        <Button type="primary" htmlType="submit">Search</Button>
                        <Button htmlType="button" onClick={reset}>Reset</Button>
                    </Space>
                </Form.Item>
            </Form>

            <Button type="primary" onClick={() => showModal(<NoteFormPage />,  {
                title: "Add note",
                styleName: 'wide',
                onClose: (data) => {
                    if (data?.reload) {

                        reload();
                    }
                }
            })}>Add</Button>

            <Table<DataType>
                columns={columns}
                dataSource={data}
                rowKey="id"
                loading={loading}
                pagination={{
                    ...pagination,
                    showSizeChanger: true,
                    pageSizeOptions: ["5", "10", "20", "50"],
                }}
                onChange={handleTableChange}
                scroll={{ x: "max-content" }}
            />
        </div>
    );
};
