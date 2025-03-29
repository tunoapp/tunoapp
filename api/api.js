import { exec } from "child_process";
import { promisify } from "util";

const execPromise = promisify(exec);

export default async function handler(req, res) {
    try {
        const { stdout } = await execPromise("php index.php");
        res.status(200).send(stdout);
    } catch (error) {
        res.status(500).send(error.message);
    }
}