import React from "react";
import {EditorHandle} from "./Editor";
import {Space} from "ui"
import {ArrowUpOutlined, ArrowDownOutlined, PlusOutlined, DeleteOutlined, VideoCameraOutlined, PictureOutlined} from "./icons"


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

    return (
        <div className="editor-tooltip">
            <Space wrap={true}>
                <button
                    onClick={() => {
                        editor?.edit(boxKey, {type: 'visual'})
                    }}
                    type="button"
                    className={boxType == 'visual' ? 'active' : ''}
                >
                    VISUAL
                </button>
                <button
                    onClick={() => {
                        editor?.edit(boxKey, {type: 'html'})
                    }}
                    type="button"
                    className={boxType == 'html' ? 'active' : ''}
                >
                    HTML
                </button>

                <button
                    onClick={() => {
                        editor?.edit(boxKey, {type: 'php'})
                    }}
                    type="button"
                    className={boxType == 'php' ? 'active' : ''}
                >
                    PHP
                </button>

                <button
                    onClick={() => {
                        editor?.edit(boxKey, {type: 'video'})
                    }}
                    type="button"
                    className={boxType == 'video' ? 'active' : ''}
                >
                    <VideoCameraOutlined/>
                </button>

                <button
                    onClick={() => {
                        editor?.edit(boxKey, {type: 'image'})
                    }}
                    type="button"
                    className={boxType == 'image' ? 'active' : ''}
                >
                    <PictureOutlined/>
                </button>
                <button onClick={() => {
                    editor?.move(boxKey, -1)
                }} type="button">
                    <ArrowUpOutlined/>
                </button>

                <button onClick={() => {
                    editor?.move(boxKey, 1)
                }} type="button">
                    <ArrowDownOutlined/>
                </button>

                <button onClick={() => {
                    editor?.add(boxKey, {type: 'visual'})
                }} type="button">
                    <PlusOutlined/>
                </button>

                <button onClick={() => {
                    editor?.delete(boxKey)
                }} type="button">
                    <DeleteOutlined/>
                </button>

                <button onClick={() => {
                    editor.beautify(boxKey)
                }} type="button">
                    Beautify
                </button>

                <button onClick={() => {
                    editor.debug(boxKey)
                }} type="button">
                    DEBUG
                </button>
            </Space>
        </div>
    );
}

export default EditorToolBar;
