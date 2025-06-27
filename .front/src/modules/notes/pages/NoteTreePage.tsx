import React, { useEffect, useState } from 'react';
import { DownOutlined } from '@ui/icons';
import {Input, Tree, Space, Spin, SearchInput} from 'ui';
import type { TreeDataNode } from '@ui/types';
import { api, route } from "api";
import {convertTreeData} from "@/utils/ui.ts";
import {Link, useNavigate } from "react-router-dom";


export const NoteTreePage: React.FC = () => {
    const [search, setSearch] = useState('');
    const [originalTreeData, setOriginalTreeData] = useState<TreeDataNode[]>([]);
    const [treeData, setTreeData] = useState<TreeDataNode[]>([]);
    const [expandedKeys, setExpandedKeys] = useState<React.Key[]>([]);
    const [loading, setLoading] = useState(true);
    const [links, setLinks] = useState(true);
    const [reloadTrigger, setReloadTrigger] = useState(0);
    const navigate = useNavigate();

    const fetchLinks = async () => {
        const response = await api.safeRequest("main.links");
        if (response.success) {
            setLinks(response.data.data.html)
        }
    };

    const fetchCategories = async () => {
        const response = await api.safeRequest("notes.categories.tree");
        if (response.success) {

            const data = convertTreeData(response.data.categories || [], { id: "key", name: "title" });

            setOriginalTreeData(data);
            searchInTree('', data);
        }
        setLoading(false);
    };

    useEffect(() => {
        setLoading(true);
        fetchCategories();
        fetchLinks();
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
        <>
            <div dangerouslySetInnerHTML={{ __html: links }} />
            <Space direction="vertical" style={{width: "100%"}}>
                <SearchInput
                    route="notes.dropdown"
                    placeholder="Search note"
                    onChange={(value) => { value && navigate(route('notes.view', {note_id: value}))}}
                    style={{width: "100%"}}
                />
                <Spin spinning={loading}>
                    <Input placeholder="Search category" value={search} onChange={onSearch} style={{ marginBottom: 8 }} />
                    <Tree
                        showLine
                        switcherIcon={<DownOutlined />}
                        expandedKeys={expandedKeys}
                        onExpand={(keys: React.Key[]) => setExpandedKeys(keys)}
                        treeData={treeData}
                    />
                </Spin>
            </Space>
        </>
    );
};

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
    if (false)
        reload();
    return (
        <div>
            <Space>
                <Link to={route('notes', {categories: id})}>{title}</Link>
            </Space>
        </div>
    );
};

