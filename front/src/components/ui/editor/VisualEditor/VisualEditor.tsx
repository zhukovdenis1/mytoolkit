import '@/css/ui/tiptap.css';
import {
    BubbleMenu,
    EditorContent,
    useEditor,
} from '@tiptap/react';
import StarterKit from '@tiptap/starter-kit';
import Link from '@tiptap/extension-link';
import { Table } from '@tiptap/extension-table';
import { TableRow } from '@tiptap/extension-table-row';
import { TableHeader } from '@tiptap/extension-table-header';
import { TableCell } from '@tiptap/extension-table-cell';
import React, { useState } from 'react';
import LinkModal from './LinkModal';
import { buildLinkAttributes } from './LinkHelpers';
import {
    UnorderedListOutlined,
    TableOutlined,
    LinkOutlined,
    PlusSquareOutlined,
    PlusOutlined ,
    MinusSquareOutlined,
    MinusOutlined,
    SmallDashOutlined
} from '../icons'
import { Dropdown, Menu } from 'antd';
import { useEffect } from "react";


import type { VisualEditorProps, LinkData } from './types';
//🔗

const VisualEditor: React.FC<VisualEditorProps> = ({ value, onChange, disabled, mode }) => {
    disabled;
    const [isLinkModalOpen, setLinkModalOpen] = useState(false);
    const [linkData, setLinkData] = useState<LinkData>({
        href: '',
        target: '_self',
        class: '',
    });

    const CustomTableCell = TableCell.extend({
        addAttributes() {
            return {
                // extend the existing attributes …
                //...this.parent?.(),

                // and add a new one …
                backgroundColor: {
                    default: null,
                    parseHTML: element => element.getAttribute('data-background-color'),
                    renderHTML: attributes => {
                        return {
                            'data-background-color': attributes.backgroundColor,
                            style: `background-color: ${attributes.backgroundColor}`,
                        }
                    },
                },
            }
        },
    })

    const editor = useEditor({
        extensions: [
            StarterKit,
            Link.configure({ openOnClick: (mode === 'view') }),
            Table.configure({
                resizable: true,
            }), // Добавляем расширение Table
            TableRow, // Добавляем расширение TableRow
            TableHeader, // Добавляем расширение TableHeader
            //TableCell, // Добавляем расширение TableCell
            CustomTableCell
        ],
        content: value,
        onUpdate: ({ editor }) => {return onChange(editor.getHTML())},
    });

    // После инициализации редактора
    useEffect(() => {
        if (editor && editor.getHTML() !== value) {
            editor.commands.setContent(value, false); // false — без сохранения в историю (чтобы не засорять undo)
        }
    }, [value, editor]);

    const openLinkModal = () => {
        if (!editor) return;

        const { href = '', target = '_self', class: className = '' } = editor.getAttributes('link');

        setLinkData({ href, target, class: className });
        setLinkModalOpen(true);
    };

    const applyLink = (data: LinkData) => {
        const attributes = buildLinkAttributes(data);
        if (data.href) {
            editor?.chain().focus().setLink(attributes).run();
        } else {
            editor?.chain().focus().unsetLink().run();
        }
        setLinkModalOpen(false);
    };

    // Меню для операций с таблицами
    const tableMenu = (
        <Menu>
            <Menu.Item
                key="insert-table"
                onClick={() => editor?.chain().focus().insertTable({ rows: 3, cols: 3, withHeaderRow: true }).run()}
            >
                <PlusSquareOutlined /> table
            </Menu.Item>
            <Menu.Item
                key="add-column"
                onClick={() => editor?.chain().focus().addColumnAfter().run()}
                disabled={!editor?.can().addColumnAfter()}
            >
                <PlusOutlined /> column
            </Menu.Item>
            <Menu.Item
                key="add-row"
                onClick={() => editor?.chain().focus().addRowAfter().run()}
                disabled={!editor?.can().addRowAfter()}
            >
                <PlusOutlined /> row
            </Menu.Item>
            <Menu.Item
                key="delete-column"
                onClick={() => editor?.chain().focus().deleteColumn().run()}
                disabled={!editor?.can().deleteColumn()}
            >
                <MinusOutlined /> column
            </Menu.Item>
            <Menu.Item
                key="delete-row"
                onClick={() => editor?.chain().focus().deleteRow().run()}
                disabled={!editor?.can().deleteRow()}
            >
                <MinusOutlined /> row
            </Menu.Item>
            <Menu.SubMenu key="extra" title=<SmallDashOutlined />>
                <Menu.Item
                    key="add-row-before"
                    onClick={() => editor?.chain().focus().addRowBefore().run()}
                    disabled={!editor?.can().addRowBefore()}
                >
                    <PlusOutlined/> row before
                </Menu.Item>
                <Menu.Item
                    key="add-column-before"
                    onClick={() => editor?.chain().focus().addColumnBefore().run()}
                    disabled={!editor?.can().addColumnBefore()}
                >
                    <PlusOutlined/> column before
                </Menu.Item>
                <Menu.Item
                    key="split-cell"
                    onClick={() => editor?.chain().focus().toggleHeaderRow().run()}
                    disabled={!editor?.can().toggleHeaderRow()}
                >
                    toggleHeaderRow
                </Menu.Item>
                <Menu.Item
                    key="split-cell"
                    onClick={() => editor?.chain().focus().toggleHeaderCell().run()}
                    disabled={!editor?.can().toggleHeaderCell()}
                >
                    toggleHeaderCell
                </Menu.Item>
                <Menu.Item
                    key="split-cell"
                    onClick={() => editor?.chain().focus().mergeOrSplit().run()}
                    disabled={!editor?.can().mergeOrSplit()}
                >
                    mergeOrSplit
                </Menu.Item>
                <Menu.Item
                    key="split-cell"
                    onClick={() => editor?.chain().focus().setCellAttribute('backgroundColor', '#FAF594').run()}
                    disabled={!editor?.can().setCellAttribute('backgroundColor', '#FAF594')}
                >
                    setCellAttribute
                </Menu.Item>
            </Menu.SubMenu>
            <Menu.Item
                key="delete-table"
                onClick={() => editor?.chain().focus().deleteTable().run()}
                disabled={!editor?.can().deleteTable()}
            >
                <MinusSquareOutlined /> таблицу
            </Menu.Item>
        </Menu>
    );

    return (
        <div className="visual-editor-container">
            {editor && (
                <BubbleMenu className="bubble-menu" tippyOptions={{duration: 100, maxWidth: 'auto'}} editor={editor}>
                    <button
                        type="button"
                        onClick={() => editor.chain().focus().toggleBold().run()}
                        className={editor.isActive('bold') ? 'is-active' : ''}
                    >
                        <b>B</b>
                    </button>
                    <button
                        type="button"
                        onClick={() => editor.chain().focus().toggleItalic().run()}
                        className={editor.isActive('italic') ? 'is-active' : ''}
                    >
                        <i>I</i>
                    </button>
                    <button
                        type="button"
                        onClick={() => editor.chain().focus().toggleStrike().run()}
                        className={editor.isActive('strike') ? 'is-active' : ''}
                    >
                        <s>S</s>
                    </button>
                    <button
                        type="button"
                        onClick={() => editor.chain().focus().toggleBulletList().run()}
                        className={editor.isActive('bulletList') ? 'is-active' : ''}
                    >
                        <UnorderedListOutlined />
                    </button>
                    <button
                        type="button"
                        onClick={() => editor.chain().focus().toggleCode().run()}
                        className={editor.isActive('code') ? 'is-active' : ''}
                    >
                        &lt;&gt;
                    </button>
                    <button
                        type="button"
                        onClick={() => editor.chain().focus().toggleHeading({level: 2}).run()}
                        className={editor.isActive('heading', {level: 2}) ? 'is-active' : ''}
                    >
                        H2
                    </button>
                    <button
                        type="button"
                        onClick={() => editor.chain().focus().toggleHeading({level: 3}).run()}
                        className={editor.isActive('heading', {level: 3}) ? 'is-active' : ''}
                    >
                        H3
                    </button>
                    <button
                        title="Link"
                        type="button"
                        onClick={openLinkModal}
                        className={editor.isActive('link') ? 'is-active' : ''}
                    >
                        <LinkOutlined />
                    </button>
                    <Dropdown overlay={tableMenu} trigger={['click']}>
                        <button
                            title="Table operations"
                            type="button"
                        >
                            <TableOutlined />
                        </button>
                    </Dropdown>
                </BubbleMenu>
            )}

            <div className="visual-editor-content">
                <EditorContent editor={editor}/>
            </div>

            {isLinkModalOpen && (
                <LinkModal
                    initialData={linkData}
                    onSave={applyLink}
                    onCancel={() => setLinkModalOpen(false)}
                    onUnlink={() => {
                        editor?.chain().focus().unsetLink().run();
                        setLinkModalOpen(false);
                    }}
                    isEditingLink={editor?.isActive('link') ?? false}
                />
            )}
        </div>
    );
};

export default VisualEditor;
