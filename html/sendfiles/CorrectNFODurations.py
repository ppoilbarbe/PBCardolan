#!/usr/bin/python
# -*- coding: UTF-8 -*-


import os
import os.path
import optparse
import logging
import re
import codecs
import sys
import xml.etree.ElementTree

#Log:
#   V1.0.0: First version

Version	= 'CorrectNFODurations V1.0.0'

# Force UTF-8 Usage
reload(sys)
sys.setdefaultencoding('utf-8')

sys.stdout = codecs.getwriter('utf8')(sys.stdout)
sys.stderr = codecs.getwriter('utf8')(sys.stderr)

class ProgramError(Exception):
    '''Class used to trap exceptions specific to the program'''
    pass

def GetUnicodeString(Source):
    """Ensure that a string is a unicode string.
       Files read on input can be Unicode or not but we don't know. If not
       we can't determine which 8bit set is used so we suppose that it is
       Latin9 (iso-8859-15)
    Returns a unicode string"""

    if Source is unicode:
      return Source
    for encoding in ('ascii', 'utf8', 'iso-8859-15'):
        try:
                return Source.decode(encoding)
        except (UnicodeDecodeError, UnicodeEncodeError):
            pass
    return Source.decode('ascii', 'ignore')


class NfoFileList:
    'Stores all the .nfo files found.'
    def __init__(self):
        self.__Content   = []

    def AddFile(self, file):
        '''Adds a file to rename if it has a .nfo description
        Checks if the given file has a corresponding .nfo file and add
        all files corresponding to the .nfo file
        '''
        logging.debug("Adding file %s", file)
        Name = self.__CheckExtension(file)
        if Name != '':
            self.__Content.append(file)

    def List(self):
        '''Returns an iterable on the file list'''
        return self.__Content

    def __CheckExtension(self, file):
        'Find nfo file'
        (FileNoExt, FileExt)   = os.path.splitext(os.path.normpath(file))
        if FileExt == '.nfo':
            return file
        return ''


def DoCheck(Options, Name):
    '''Do the job of openin the file, finding dureations and rewrite
    the file with correct duration if it is invalid'''

    NfoFile = Name
    file= open(Name)
    FileContent	= []
    ReDuration	= re.compile('^(\s*\<runtime\>)\s*(\d+)\s*h\s*(\d+)\s*min\s*(\</runtime\>\s*)$', re.I);
    Modified	= False
    
    for line in file:
	m = re.search(ReDuration, line)
	if m:
	    newline = "%s%02dh%02dmin%s" % (m.group(1), int(m.group(2)), int(m.group(3)), m.group(4))
	    if line != newline:
		Modified	= True
		logging.debug("File %s: %s", Name, line)
		logging.debug("File %s: %s", Name, newline)
		line = newline
	FileContent.append(line)
    file.close;
    if not Modified:
	return
    if Options.backup:
	BackupFile = Name + Options.backupextension
	if not os.path.exists(BackupFile):
	    logging.debug("Backup of '%s' to '%s'", Name, BackupFile)
	    os.rename(Name, BackupFile)
    logging.debug("Rewrite '%s'", Name)
    file	= open(Name, "w")
    file.writelines(FileContent)
    file.close

def main():
    '''Interprets the command line and do the job'''
    global Version
    usage = "%prog [options]  {file|dir}...\n  %prog --help"
    description = "Correct invalid durations in a .nfo file for XBMC. " \
                  "A correct duration is in format 'XXhXXmin'. Where XX is "\
		  "the hour and YY the minutes (without any space).\n" \
		  "Otherwise incorrect treatments are done, specially when sorting by duration."
    parser = optparse.OptionParser(usage = usage,
                          description = description,
			  version = Version)


    parser.add_option("-d", "--debug",
                      action="store_true", dest="debug", default=False,
                      help="shows lot of nasty debug informations.")

    parser.add_option("", "--recurse", action="store_true", dest="recurse",
                      help="Recurse in subdirectories if a directory is found "
                           "in a given directory"
                           " [default]")
    parser.add_option("", "--norecurse", action="store_false", dest="recurse",
                      default=True,
                      help="Don't recurse in subdirectories if a directory is found "
                           "in a given directory")
    parser.add_option("", "--nobackup", action="store_false", dest="backup",
                      default=True,
                      help="Don't create a backup of nfo file (default is to create one).")
    parser.add_option("", "--backupextension", dest="backupextension",
                      default=".runtime_backup",
                      help="Extension used for backup files (if created) " \
			" [default=\"%default\"].")

    (options, args) = parser.parse_args()
    if len(args) < 1:
        parser.error("incorrect number of arguments")
    if options.debug:
        logging.basicConfig(level=logging.DEBUG)
    filelist    = NfoFileList()
    index       = 0
    while index < len(args):
        name    = args[index]
        logging.debug("Name[%d]=%s", index, name)
        index   += 1
        if not os.path.exists(name):
            continue
        name    = os.path.abspath(name)
        if os.path.isdir(name):
            for content in os.listdir(name):
                fullname    = os.path.join(name, content)
                if (not os.path.isdir(fullname)) or options.recurse:
                    args.append(fullname)
            continue
        filelist.AddFile(name)
    for name in filelist.List():
        DoCheck(options, name)

if __name__ == "__main__":
    try:
        main()
    except ProgramError, e:
        print "Error: "+str(e)

