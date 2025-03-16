import React from "react";
import {EditorHandle} from "./Editor";
import {Space, Dropdown, Menu} from "ui"
import {ArrowUpOutlined, ArrowDownOutlined, PlusOutlined, DeleteOutlined, VideoCameraOutlined, PictureOutlined, CodeOutlined, EyeOutlined} from "./icons"


type EditorToolBarProps = {
    boxKey: string;
    boxType: string;
    editor: EditorHandle;
    mode: string;
};

const EditorToolBar: React.FC<EditorToolBarProps> = ({ boxKey, boxType, editor, mode }) => {
    if (mode == 'view') {
        return null;
    }

    // Обработчик для выбора языка в выпадающем меню
    const handleCodeMenuClick = (language: string) => {
        editor?.edit(boxKey, { type: "code", data: { language } });
    };

    // Меню для выпадающего списка "Code"
    const codeMenu = (
        <Menu>
            <Menu.Item key="html" onClick={() => handleCodeMenuClick("html")}>
                HTML
            </Menu.Item>
            <Menu.Item key="php" onClick={() => handleCodeMenuClick("php")}>
                PHP
            </Menu.Item>
        </Menu>
    );

    return (
        <div className="editor-tooltip">
            <Space wrap={true}>
                <button
                    onClick={() => {
                        editor?.edit(boxKey, {type: 'visual'})
                    }}
                    type="button" title="Visual editor"
                    className={boxType == 'visual' ? 'active' : ''}
                >
                    <EyeOutlined />
                </button>
                <Dropdown overlay={codeMenu} trigger={["click"]}>
                    <button
                        type="button"
                        title="Code"
                        className={boxType == "code" ? "active" : ""}
                    >
                        <CodeOutlined />
                    </button>
                </Dropdown>

                <button
                    onClick={() => {
                        editor?.edit(boxKey, {type: 'video'})
                    }}
                    type="button" title="Video"
                    className={boxType == 'video' ? 'active' : ''}
                >
                    <VideoCameraOutlined/>
                </button>

                <button
                    onClick={() => {
                        editor?.edit(boxKey, {type: 'image'})
                    }}
                    type="button" title="Image"
                    className={boxType == 'image' ? 'active' : ''}
                >
                    <PictureOutlined/>
                </button>
                <button onClick={() => {
                    editor?.move(boxKey, -1)
                }} type="button" title="Up">
                    <ArrowUpOutlined/>
                </button>

                <button onClick={() => {
                    editor?.move(boxKey, 1)
                }} type="button" title="Down">
                    <ArrowDownOutlined/>
                </button>

                <button onClick={() => {
                    editor?.add(boxKey, {type: 'visual'})
                }} type="button" title="Add block">
                    <PlusOutlined/>
                </button>

                <button onClick={() => {
                    editor?.delete(boxKey)
                }} type="button" title="Delete this block">
                    <DeleteOutlined/>
                </button>

                {/*<button onClick={() => {*/}
                {/*    editor.beautify(boxKey)*/}
                {/*}} type="button">*/}
                {/*    Beautify*/}
                {/*</button>*/}

                {/*<button onClick={() => {*/}
                {/*    editor.debug(boxKey)*/}
                {/*}} type="button">*/}
                {/*    DEBUG*/}
                {/*</button>*/}

            </Space>
        </div>
    );
}

export default EditorToolBar;
