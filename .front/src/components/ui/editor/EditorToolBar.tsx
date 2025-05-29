import React from "react";
import {EditorHandle} from "./Editor";
import {Space, Dropdown, Confirmable} from "ui"
import {ArrowUpOutlined, ArrowDownOutlined, PlusOutlined, DeleteOutlined, VideoCameraOutlined
    , PictureOutlined, CodeOutlined, EyeOutlined, EyeInvisibleOutlined, ShoppingCartOutlined} from "./icons"


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
    const handleCodeMenuClick = (language: string, text: string) => {
        editor?.edit(boxKey, { type: "code", data: { language: language, text: text } });
    };

    const getCodeMenu = (boxKey: string) => {
        const language = (editor.getBox(boxKey)).data?.language ?? '';
        const text = (editor.getBox(boxKey)).data?.text ?? '';
        return ({
            items: [
                {
                    key: "php",
                    label: "PHP",
                    onClick: () => handleCodeMenuClick("php", text),
                },
                {
                    key: "sql",
                    label: "SQL",
                    onClick: () => handleCodeMenuClick("sql", text),
                },
                {
                    key: "html",
                    label: "HTML",
                    onClick: () => handleCodeMenuClick("html", text),
                },
                {
                    key: "css",
                    label: "CSS",
                    onClick: () => handleCodeMenuClick("css", text),
                },
                {
                    key: "json",
                    label: "JSON",
                    onClick: () => handleCodeMenuClick("json", text),
                },
            ],
            selectedKeys: [language]
        });
    }


    //const codeMenu = {};

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
                    <EyeOutlined/>
                </button>
                <button
                    onClick={() => {
                        editor?.edit(boxKey, {type: 'visualSource'})
                    }}
                    type="button" title="Visual source editor"
                    className={boxType == 'visualSource' ? 'active' : ''}
                >
                    <EyeInvisibleOutlined/>
                </button>

                <button
                    onClick={() => {
                        editor?.edit(boxKey, {type: 'product'})
                    }}
                    type="button" title="Product"
                    className={boxType == 'product' ? 'active' : ''}
                >
                    <ShoppingCartOutlined />
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

                <button
                    onClick={() => {
                        editor?.edit(boxKey, {type: 'video'})
                    }}
                    type="button" title="Video"
                    className={boxType == 'video' ? 'active' : ''}
                >
                    <VideoCameraOutlined/>
                </button>

                <Dropdown menu={getCodeMenu(boxKey)} trigger={["click"]}>
                    <button
                        type="button"
                        title="Code"
                        className={boxType == "code" ? "active" : ""}
                    >
                        <CodeOutlined/>
                    </button>
                </Dropdown>

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

                <Confirmable onConfirm={() => editor?.delete(boxKey)}>
                    <button type="button" title="Delete this block">
                        <DeleteOutlined/>
                    </button>
                </Confirmable>

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
