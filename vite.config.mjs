import { dirname, resolve } from 'path';
import { fileURLToPath } from 'url';
import browserslistToEsbuild from 'browserslist-to-esbuild';

const __dirname = dirname( fileURLToPath( import.meta.url ) );

export default {
	build: {
		target: browserslistToEsbuild(),
		outDir: 'assets',
		emptyOutDir: true,
		sourcemap: false,
		rollupOptions: {
			input: resolve( __dirname, 'resources/widgetizer.js' ),
			output: {
				entryFileNames: '[name].js',
				chunkFileNames: '[name]-[hash].js',
				assetFileNames: '[name][extname]',
			},
		},
	},
};
