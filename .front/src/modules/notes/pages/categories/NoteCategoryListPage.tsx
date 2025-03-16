import React, { useEffect, useState } from 'react';
import { DownOutlined } from '@ui/icons';
import { Input, Tree, Space, Spin, Button, message, showModal, Confirmable } from 'ui';
import type { TreeDataNode } from '@ui/types';
import { api } from "api";
import { NoteCategoryFormPage } from "./NoteCategoryFormPage";
import {convertTreeData} from "@/utils/ui.ts";

export const NoteCategoryListPage: React.FC = () => {
    const [search, setSearch] = useState('');
    const [originalTreeData, setOriginalTreeData] = useState<TreeDataNode[]>([]);
    const [treeData, setTreeData] = useState<TreeDataNode[]>([]);
    const [expandedKeys, setExpandedKeys] = useState<React.Key[]>([]);
    const [loading, setLoading] = useState(true);
    const [reloadTrigger, setReloadTrigger] = useState(0);

    const fetchCategories = async () => {
        const response = await api.safeRequest("notes.categories.tree");
        if (response.data.success) {
            const data = convertTreeData(response.data.data || [], { id: "key", name: "title" });
            setOriginalTreeData(data);
            searchInTree('', data);
        }
        setLoading(false);
    };

    useEffect(() => {
        setLoading(true);
        fetchCategories();
    }, [reloadTrigger]);

    const onSearch = (e: React.ChangeEvent<HTMLInputElement>) => {
        const value = e.target.value;
        searchInTree(value, originalTreeData);
    };

    const searchInTree = (value: string, treeData: TreeDataNode[]) => {
        setSearch(value);
        const [filteredData, keys] = filterTree(treeData, value);
        setTreeData(filteredData);
        setExpandedKeys(keys);
    };

    const filterTree = (data: TreeDataNode[], search: string): [TreeDataNode[], React.Key[]] => {
        let expandedKeys: React.Key[] = [];

        const filterNode = (node: TreeDataNode): TreeDataNode | null => {
            const highlightedTitle = highlightText(node, search);

            if (node.children) {
                const filteredChildren = node.children.map(filterNode).filter(Boolean) as TreeDataNode[];
                if (filteredChildren.length > 0 || (node.title && typeof node.title === 'string' && node.title.toLowerCase().includes(search.toLowerCase()))) {
                    expandedKeys.push(node.key);
                    return {
                        ...node,
                        children: filteredChildren,
                        title: nodeHtml(node.key as string, highlightedTitle as string, () => setReloadTrigger(prev => prev + 1))
                    };
                }
            }
            if (node.title && typeof node.title === 'string' && node.title.toLowerCase().includes(search.toLowerCase())) {
                return {
                    ...node,
                    title: nodeHtml(node.key as string, highlightedTitle as string, () => setReloadTrigger(prev => prev + 1))
                };
            }

            return null;
        };

        return [data.map(filterNode).filter(Boolean) as TreeDataNode[], expandedKeys];
    };

    return (
        <Spin spinning={loading}>
            <Input placeholder="Search" value={search} onChange={onSearch} style={{ marginBottom: 8 }} />
            <Tree
                showLine
                switcherIcon={<DownOutlined />}
                expandedKeys={expandedKeys}
                onExpand={(keys: React.Key[]) => setExpandedKeys(keys)}
                treeData={treeData}
            />
            <Button
                type="primary"
                htmlType="button"
                onClick={() =>
                    showModal(<NoteCategoryFormPage />, {
                        title: "Add category",
                        loading: true,
                        onClose: (data: { reload?: boolean }) => {
                            if (data?.reload) {
                                setReloadTrigger(prev => prev + 1);
                            }
                        },
                    })
                }
            >
                Add
            </Button>
        </Spin>
    );
};

// const formatTreeData = (data: any[]): TreeDataNode[] => {
//     return data.map(item => {
//         const { id, name } = item;
//         return {
//             key: id.toString(),
//             title: name.toString(),
//             children: item.children ? formatTreeData(item.children) : undefined,
//         };
//     });
// };

const highlightText = (node: TreeDataNode, search: string): React.ReactNode => {
    const text = node.title as string;
    if (!search) return text;
    const parts = text.split(new RegExp(`(${search})`, 'gi'));
    return parts.map((part, index) =>
        part.toLowerCase() === search.toLowerCase() ? (
            <span key={index} style={{ backgroundColor: 'yellow' }}>{part}</span>
        ) : (
            part
        )
    );
};

const nodeHtml = (id: string, title: React.ReactNode, reload: () => void = () => { }) => {
    return (
        <div>
            <Space>
                <span className="title">{title}</span>
                <Button
                    type="link"
                    htmlType="button"
                    onClick={() =>
                        showModal(<NoteCategoryFormPage />, {
                            title: "Add category",
                            data: { parentId: parseInt(id) },
                            loading: true,
                            onClose: (data: { reload?: boolean }) => {
                                if (data?.reload) {
                                    reload();
                                }
                            },
                        })
                    }
                >
                    add
                </Button>
                <Button
                    type="link"
                    onClick={() =>
                        showModal(<NoteCategoryFormPage />, {
                            title: "Edit category",
                            data: { id: parseInt(id) },
                            loading: true,
                            onClose: (data: { reload?: boolean }) => {
                                if (data?.reload) {
                                    reload();
                                }
                            },
                        })
                    }
                >
                    edit
                </Button>
                <Confirmable onConfirm={() => deleteCategory(id, reload)}>
                    <Button type="link">delete</Button>
                </Confirmable>
            </Space>
        </div>
    );
};

const deleteCategory = async (categoryId: string, reload: () => void) => {
    const response = await api.safeRequest(`notes.categories.delete`, { category_id: categoryId });
    if (response && typeof response !== 'boolean' && response.data && response.data.success) {
        reload();
        message.success('Category was deleted successfully');
    } else {
        message.error('Deleting failed');
    }
};
