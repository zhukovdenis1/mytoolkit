import '@/css/ui/tiptap.css';

import {
    BubbleMenu,
    EditorContent,
    useEditor,
} from '@tiptap/react';
import React, { useState } from 'react';
import LinkModal from './LinkModal';
import { buildLinkAttributes } from './LinkHelpers';
import {
    UnorderedListOutlined,
    TableOutlined,
    LinkOutlined,
} from '../icons'
import { Dropdown } from 'antd';
import { useEffect } from "react";
import { getUserConfig } from './userConfig';
import { getAdminConfig } from './adminConfig';
import {getTableMenu} from './tableConfig.tsx';

import type { VisualEditorProps, LinkData } from './types';
//🔗

const VisualEditor: React.FC<VisualEditorProps> = ({ data, onChange, disabled, mode }) => {
    disabled;
    mode;
    const [isLinkModalOpen, setLinkModalOpen] = useState(false);
    const [linkData, setLinkData] = useState<LinkData>({
        href: '',
        target: '_self',
        class: '',
    });


    // const CustomTableCell = TableCell.extend({
    //     addAttributes() {
    //         return {
    //             // extend the existing attributes …
    //             ...this.parent?.(),
    //
    //             // and add a new one …
    //             backgroundColor: {
    //                 default: null,
    //                 parseHTML: element => element.getAttribute('data-background-color'),
    //                 renderHTML: attributes => {
    //                     return {
    //                         'data-background-color': attributes.backgroundColor,
    //                         style: `background-color: ${attributes.backgroundColor}`,
    //                     }
    //                 },
    //             },
    //         }
    //     },
    // })

    const isAdmin = true;

    const editor = useEditor({
        // extensions: [
        //     StarterKit,
        //     Link.configure({ openOnClick: (mode === 'view') }),
        //     Table.configure({
        //         resizable: true,
        //     }), // Добавляем расширение Table
        //     TableRow, // Добавляем расширение TableRow
        //     TableHeader, // Добавляем расширение TableHeader
        //     //TableCell, // Добавляем расширение TableCell
        //     CustomTableCell
        // ],
        extensions: isAdmin ? getAdminConfig() : getUserConfig(),
        content: data.text,
        onUpdate: ({ editor }) => {return onChange({text: editor.getHTML()})},
    });

    // После инициализации редактора
    useEffect(() => {
        if (editor && editor.getHTML() !== data.text) {
            editor.commands.setContent(data.text, false); // false — без сохранения в историю (чтобы не засорять undo)
        }
    }, [data.text, editor]);

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
    const tableMenu = getTableMenu(editor);


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
                    <Dropdown menu={tableMenu} trigger={['click']}>
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
