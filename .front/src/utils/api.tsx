export { api } from "@/services/api";
export { route } from "./route";

export const log = {
    error: (data: any) => {console.error("[ERROR]", data); alert('Error: ' + data)},
    info: (data: any) => console.log("[INFO]", data),
};
