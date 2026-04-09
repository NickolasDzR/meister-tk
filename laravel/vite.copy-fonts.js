// copy-fonts.js
import fs from 'fs';
import path from 'path';

const srcDir = path.resolve('resources/fonts');
const destDir = path.resolve('public/fonts');

if (fs.existsSync(srcDir)) {
    if (!fs.existsSync(destDir)) {
        fs.mkdirSync(destDir, { recursive: true });
    }

    const files = fs.readdirSync(srcDir);
    files.forEach(file => {
        if (file.endsWith('.woff') || file.endsWith('.woff2')) {
            fs.copyFileSync(
                path.join(srcDir, file),
                path.join(destDir, file)
            );
            console.log(`✓ Скопирован: ${file}`);
        }
    });
}