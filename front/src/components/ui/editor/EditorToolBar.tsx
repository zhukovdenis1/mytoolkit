import React from "react";
 import {EditorHandle} from "./Editor";

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
            <button
                onClick={() => {
                    editor?.edit(boxKey, {type: 'visual'})
                }}
                type="button"
                className={boxType == 'visual' ? 'active' : ''}
            >
                VISUAL
            </button>

            &nbsp;

            <button
                onClick={() => {
                    editor?.edit(boxKey, {type: 'html'})
                }}
                type="button"
                className={boxType == 'html' ? 'active' : ''}
            >
                HTML
            </button>

            &nbsp;

            <button
                onClick={() => {
                    editor?.edit(boxKey, {type: 'php'})
                }}
                type="button"
                className={boxType == 'php' ? 'active' : ''}
            >
                PHP
            </button>

            &nbsp;

            <button
                onClick={() => {
                    editor?.edit(boxKey, {type: 'video'})
                }}
                type="button"
                className={boxType == 'video' ? 'active' : ''}
            >
                Video
            </button>

            &nbsp;

            <button
                onClick={() => {
                    editor?.edit(boxKey, {type: 'image'})
                }}
                type="button"
                className={boxType == 'image' ? 'active' : ''}
            >
                IMG
            </button>

            &nbsp;

            <button onClick={() => {
                editor?.move(boxKey, -1)
            }} type="button">
                UP
            </button>

            &nbsp;

            <button onClick={() => {
                editor?.move(boxKey, 1)
            }} type="button">
                DOWN
            </button>

            &nbsp;

            <button onClick={() => {
                editor?.add(boxKey, {type: 'visual'})
            }} type="button">
                ADD
            </button>

            &nbsp;

            <button onClick={() => {
                editor?.delete(boxKey)
            }} type="button">
                DEL
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

        </div>
    );
}

export default EditorToolBar;
