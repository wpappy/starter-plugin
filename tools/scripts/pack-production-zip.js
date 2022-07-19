const rootPath = '../..';
const configName = 'production-zip.config.json';
const destDirName = 'production-zip';

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

		const refsDefined = config.hasOwnProperty( 'directories' ) && Array.isArray( config.directories ) &&
			config.hasOwnProperty( 'files' ) && Array.isArray( config.files ) &&
			( config.files.length || config.files.length );

		if ( refsDefined ) {
			output.on( 'close', function() {
				console.log( '\n' );
				log.success_( '"' + config.name + '.zip" deployed to the "' + destDirName + '" directory.' );
				console.log( '\n' )
			});

			archive.on( 'error', function( err ) {
				console.error( err );
			});

			archive.pipe( output );

			let directories = config.directories;

			for ( let i = 0; i < directories.length; i++ ) {
				archive.directory( rootPath + '/' + directories[i], config.name + '/' + directories[i], null );
			}

			let files = config.files;

			for ( let i = 0; i < files.length; i++ ) {
				archive.file( rootPath + '/' + files[i], { name: config.name + '/' + files[i] });
			}

			archive.finalize();

		} else {
			console.error( '"directories" and "files" fields is required in the "' + configName + '" file and must be arrays. Also, at least one of them must be non-empty.' );
		}

	} else {
		console.error( '"name" field is required in the "' + configName + '" file and must be a string.' );
	}

} else {
	console.error( '"' + configName + '" file not found in application root directory.' );
}
