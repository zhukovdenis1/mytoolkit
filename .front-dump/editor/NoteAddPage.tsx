import React from "react";
import { Button, Form, Space } from "antd";
import Editor from "@/components/Editor";

export const NoteAddPage: React.FC = () => {
    const editor1 = Editor.useEditor();
    const editor2 = Editor.useEditor();

    editor1.onChange = function(value) {console.log('changed to'+ value);}

    const setValue1 = () => {
        editor1.setValue("Editor 1 value");
    };

    const getValue1 = () => {
        console.log("Editor 1:", editor1.getValue());
    };

    const setValue2 = () => {
        editor2.setValue("Editor 2 value");
    };

    const getValue2 = () => {
        console.log("Editor 2:", editor2.getValue());
    };

    return (
        <div>
            <h2>Add note</h2>
            <Form>
                <h3>Editor 1</h3>
                <Editor editor={editor1} />
                <Form.Item>
                    <Space>
                        <Button type="primary" htmlType="button" onClick={setValue1}>setValue Editor 1</Button>
                        <Button type="primary" htmlType="button" onClick={getValue1}>getValue Editor 1</Button>
                    </Space>
                </Form.Item>

                <h3>Editor 2</h3>
                <Editor editor={editor2} />
                <Form.Item>
                    <Space>
                        <Button type="primary" htmlType="button" onClick={setValue2}>setValue Editor 2</Button>
                        <Button type="primary" htmlType="button" onClick={getValue2}>getValue Editor 2</Button>
                    </Space>
                </Form.Item>
            </Form>
        </div>
    );
};
