const fs = require('fs');
const path = require('path');

const directory = 'c:/laragon/www/Hotel';

const ignoreDirs = ['node_modules', 'vendor', 'storage', '.git'];

function replaceInFile(filePath) {
    const ext = path.extname(filePath);
    if (!['.php', '.vue', '.js', '.css', '.env', '.md'].includes(ext) && path.basename(filePath) !== '.env') {
        return;
    }

    try {
        let content = fs.readFileSync(filePath, 'utf8');
        let original = content;
        
        content = content.replace(/Arunika Ryoka \(Miyabi Hotel\)/g, 'Darma Mizuki');
        content = content.replace(/Arunika Ryoka/g, 'Darma Mizuki');
        content = content.replace(/Miyabi Hotel/g, 'Darma Mizuki');
        content = content.replace(/Arunika/g, 'Darma Mizuki');
        content = content.replace(/Miyabi/g, 'Darma Mizuki');

        if (content !== original) {
            fs.writeFileSync(filePath, content, 'utf8');
            console.log(`Updated: ${filePath}`);
        }
    } catch (e) {
        console.error(`Error processing ${filePath}: ${e.message}`);
    }
}

function walkDir(dir) {
    const files = fs.readdirSync(dir);
    for (const file of files) {
        const fullPath = path.join(dir, file);
        const stat = fs.statSync(fullPath);
        
        if (stat.isDirectory()) {
            if (!ignoreDirs.includes(file)) {
                walkDir(fullPath);
            }
        } else {
            replaceInFile(fullPath);
        }
    }
}

walkDir(directory);
console.log('Replacement complete.');
