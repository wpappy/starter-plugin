const rootPath = '../..';
const configName = 'prod-zip.config.json';
const destDirName = 'prod-zip';

const fs = require( 'fs' );
const archiver = require( 'archiver' );
const log = require( 'log-beautify' );

if ( fs.existsSync( rootPath + '/' + configName ) ) {
	const config = require( rootPath + '/' + configName );

	if ( config.hasOwnProperty( 'name' ) && 'string' === typeof config.name ) {
		if ( ! fs.existsSync( rootPath + '/' + destDirName ) ){
			fs.mkdirSync( rootPath + '/' + destDirName );
		}

		const output = fs.createWriteStream( rootPath + '/' + destDirName + '/' + config.name + '.zip' );
		const archive = archiver( 'zip', {});

		const refsDefined = config.hasOwnProperty( 'dirs' ) && Array.isArray( config.dirs ) &&
			config.hasOwnProperty( 'files' ) && Array.isArray( config.files ) &&
			( config.files.length || config.files.length );

		if ( refsDefined ) {
			output.on( 'close', function() {
				console.log( '\n' );
				log.success_( '"' + config.name + '.zip" deployed to the releases folder.' );
				console.log( '\n' )
			});

			archive.on( 'error', function( err ) {
				console.error( err );
			});

			archive.pipe( output );

			let dirs = config.dirs;

			for ( let i = 0; i < dirs.length; i++ ) {
				archive.directory( rootPath + '/' + dirs[i], config.name + '/' + dirs[i], null );
			}

			let files = config.files;

			for ( let i = 0; i < files.length; i++ ) {
				archive.file( rootPath + '/' + files[i], { name: config.name + '/' + files[i] });
			}

			archive.finalize();

		} else {
			console.error( '"dirs" and "files" fields is required in the "' + configName + '" file and must be arrays. Also, at least one of them must be non-empty.' );
		}

	} else {
		console.error( '"name" field is required in the "' + configName + '" file and must be a string.' );
	}

} else {
	console.error( '"' + configName + '" file not found in application root directory.' );
}
