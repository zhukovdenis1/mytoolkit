import React, { useEffect, useState } from "react";
import { Link, useNavigate, useLocation, useParams } from "react-router-dom";
import { Table, Input, Button, Form, Space, TreeSelect, message, showModal, Confirmable } from "ui";
import type { TablePaginationConfig, ColumnsType } from "@ui/types";
import { EditOutlined, DeleteOutlined } from '@ui/icons';
import { api, route } from "api";
import { NoteFormPage } from "./NoteFormPage";
import { convertTreeData } from "@/utils/ui";

interface DataType {
    id: number;
    title: string;
}

interface Params {
    search?: string;
    categories?: string | string[];
    _page?: number;
    _limit?: number;
    _sort?: string;
    _order?: string;
}

interface NoteSearchPageProps {
    action?: string
}

export const NoteSearchPage: React.FC<NoteSearchPageProps> = ({ action = '' }) => {
    const navigate = useNavigate();
    const location = useLocation();
    const [form] = Form.useForm();
    const [data, setData] = useState<DataType[]>([]);
    const [categoriesTree, setCategoriesTree] = useState<any[]>([]);
    const [loading, setLoading] = useState(false);
    const [pagination, setPagination] = useState<TablePaginationConfig>({
        current: 1,
        pageSize: 10,
        total: 0,
        showSizeChanger: true,
    });
    const [sorter, setSorter] = useState<{ field?: string; order?: string }>({});
    const { note_id: noteId } = useParams<{ note_id: string }>();

    const columns: ColumnsType<DataType> = [
        { title: "ID", dataIndex: "id", key: "id", sorter: true, width: 50 },
        {
            title: "Name",
            dataIndex: "title",
            key: "title",
            sorter: true,
            render: (_, record) => (
                <Link to={route('notes.view', { note_id: record.id })}>{record.title}</Link>
            ),
        },
        {
            title: "Actions",
            key: "action",
            fixed: "right",
            width: 50,
            render: (_, record) => (
                <Space>
                    <Button
                        type="link"
                        title="edit"
                        icon={<EditOutlined />}
                        onClick={() => showNoteModal('edit', reload, record.id)}
                    />
                    <Confirmable onConfirm={() => deleteNote(record.id)}>
                    <Button
                        type="link"
                        title="delete"
                        icon={<DeleteOutlined />}
                    />
                    </Confirmable>
                </Space>
            ),
        },
    ];

    useEffect(() => {
        if (action) {
            showNoteModal(action, reload, noteId)
        }
        const params = Object.fromEntries(new URLSearchParams(location.search).entries());
        form.setFieldsValue({
            search: params.search,
            categories: params.categories ? params.categories.split(',') : [],
        });
        setPagination((prev) => ({
            ...prev,
            current: Number(params._page) || 1,
            pageSize: Number(params._limit) || 10,
            total: Number(params._total) || 0,
        }));
        fetchData(params);
    }, [location.search]);

    const updateUrl = (params: Params) => {
        const queryString = new URLSearchParams(params as Record<string, string>).toString();
        navigate(`?${queryString}`);
    };

    const fetchData = async (params: Params = {}) => {
        setLoading(true);

        if (params.categories && typeof params.categories === "string") {
            params.categories = params.categories.split(',');
        }

        const response = await api.safeRequest("notes.search", params);
        if (response.data.success) {
            setData(response.data.data);
            setPagination((prev) => ({
                ...prev,
                current: response.data.meta.current_page,
                pageSize: response.data.meta.per_page,
                total: response.data.meta.total,
            }));
        }

        setLoading(false);

        const categoriesTreeResponse = await api.safeRequest("notes.categories.tree");
        if (categoriesTreeResponse.data.success) {
            setCategoriesTree(convertTreeData(categoriesTreeResponse.data.data, { id: 'value', name: 'title' }));
        }
    };

    const handleSearch = (values: any) => {
        const toPage = pagination.current ?? 1;
        const fromPage = new URLSearchParams(window.location.search).get('_page') ?? 1;
        console.log(`${toPage}=${fromPage}`)
        const queryParams: Params = {
            search: values.search || "",
            categories: values.categories ? values.categories.join(',') : "",
            _page: (toPage == fromPage) ? 1 : toPage,//сбрасыаем на 1, если преход не по страницам, а => меняются условия поиска
            _limit: pagination.pageSize,
            _sort: sorter.field || "",
            _order: sorter.order || "",
        };

        updateUrl(queryParams);
        fetchData(queryParams);
    };

    const reset = () => {
        navigate(route('notes.search'));
    };

    const reload = () => {
        form.submit();
    };

    const deleteNote = async (noteId: number) => {
        const response = await api.safeRequest(`notes.delete`, { note_id: noteId });
        if (response && typeof response !== 'boolean' && response.data?.success) {
            reload();
            message.success('Note was deleted successfully');
        } else {
            message.error('Deleting failed');
        }
    };

    const handleTableChange = (pag: TablePaginationConfig, _: any, sorterObj: any) => {
        const newSorter = sorterObj.order
            ? { field: sorterObj.field, order: sorterObj.order === "ascend" ? "asc" : "desc" }
            : {};

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
                        dropdownStyle={{ minWidth: 200, maxHeight: 400, overflow: 'auto' }}
                        placeholder="Please select"
                        allowClear
                        multiple
                        treeDefaultExpandAll
                        treeData={categoriesTree}
                        filterTreeNode={(input, node) =>
                            (node.title as string).toLowerCase().includes(input.toLowerCase())
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

            <Button
                type="primary"
                onClick={() => showNoteModal('add', reload, undefined, {categories: form.getFieldValue('categories')})}
            >
                Add
            </Button>

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

const showNoteModal = (action: string, reload: () => void, noteId?: string|number, noteData?: {}) => {
    if (action == 'add' || !noteId) {
        return  showModal(<NoteFormPage />, {
            title: "Add note",
            styleName: 'wide',
            data: noteData,
            url: route('notes.add'),
            onClose: (data: { reload?: boolean }) => {
                if (data?.reload) {
                    reload();
                }
            },
        })
    } else {
        return showModal(<NoteFormPage />, {
            title: "Edit note",
            loading: true,
            styleName: 'wide',
            data: { id: noteId },
            url: route('notes.edit', {note_id: noteId}),
            onClose: (data: { reload?: boolean }) => {
                if (data?.reload) {
                    reload();
                }
            },
        })
    }

}
