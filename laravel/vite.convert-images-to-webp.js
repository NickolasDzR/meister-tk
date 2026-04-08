// vite.convert-images-to-webp.js
import { glob } from 'glob';
import sharp from 'sharp';
import fs from 'fs-extra';
import path from 'path';

const sourceDir = path.resolve(process.cwd(), 'resources/images');
const targetDir = path.resolve(process.cwd(), 'public/images');

async function convertImages() {
    console.log('\nКонвертация изображений в WebP\n');

    // 1. Конвертируем PNG/JPG в WebP
    const files = await glob('**/*.{png,jpg,jpeg}', { cwd: sourceDir, absolute: true });

    if (files.length === 0) {
        console.log('Нет PNG/JPG файлов для конвертации');
    }

    for (const file of files) {
        const relativePath = path.relative(sourceDir, file);
        const targetPath = path.join(targetDir, relativePath.replace(/\.(png|jpg|jpeg)$/i, '.webp'));

        await fs.ensureDir(path.dirname(targetPath));

        console.log(`🔄 ${relativePath} → ${path.basename(targetPath)}`);

        await sharp(file)
            .webp({ quality: 80 })
            .toFile(targetPath);
    }

    // 2. Копируем существующие WebP
    const webpFiles = await glob('**/*.webp', { cwd: sourceDir, absolute: true });

    for (const file of webpFiles) {
        const relativePath = path.relative(sourceDir, file);
        const targetPath = path.join(targetDir, relativePath);

        await fs.ensureDir(path.dirname(targetPath));
        await fs.copy(file, targetPath);

        console.log(` ${relativePath} (скопирован)`);
    }

    console.log('\n Готово!\n');
}

convertImages().catch(console.error);