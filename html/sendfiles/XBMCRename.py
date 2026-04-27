#!/usr/bin/python
# -*- coding: UTF-8 -*-


import os
import os.path
import argparse
import logging
import re
import codecs
import sys
import xml.etree.ElementTree as ElementTree
import xml.parsers.expat


#Log:
#   V0.1.0: Bug correction if a tag is empty (None was returned)
#   V0.2.0: Take into account .nfo in VIDEO_TS dir (dvd structure) which
#               is named VIDEO_TS.nfo and must remain named like this.
#               in this case it is the directory which must be renamed.
#               May be the same thing is to be done for Bluray disks, but
#               I have none to test.
#               Remove some UTF-8 Problems when in debug mode and for some
#               titles
#   V0.3.0: --list, --nostart, --noend, --forcesort, --movieonly,
#               --serieonly options added.
#   V0.4.0: Frodo naming conventions taken into account
#   V0.5.0: Multiple episodes in 1 file taken into account (and no more error
#               when reading multiple xml top entries in 1 file)
#               Options added:
#                   - episodegroupedstart
#                   - episodegroupedend
#               Expandable fields in formats added:
#                   - episodegrouped
#                   - displayepisodegrouped
#               obsolete optparse module replaced by argparse


Version = 'XBMCRename V0.5.0'

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


class NfoContent:
    '''Class which reads NFO files and gives the field values'''
    def __init__(self, name):
        logging.debug("Loading xml file %s", name)
        self.__structure = []
        self.__nfotype   = 'nada'
        try:
            lines = []
            for line in codecs.open(name, "rU", "utf-8"):
                lines.append(GetUnicodeString(line))
            # To overcome multiple root for episodedetails
            lines.append(u"\n</forcedrootelement>\n")
            index = 1 if lines[0].upper().find('<?XML') >= 0 else 0
            lines.insert(index, u"\n<forcedrootelement>\n")
            FakeRoot = ElementTree.fromstring("".join(lines))
            root_count = -1
            for Root in FakeRoot:
                if Root.tag == 'forcedrootelement':
                    continue
                if len(self.__structure) == 0:
                    self.__nfotype  = Root.tag
                    logging.debug("    nfo type: %s", self.__nfotype)
                structure = {}
                for elem in Root:
                    if elem.text != '':
                        tag = elem.tag
                        if (tag == 'actor' or
                            tag == 'thumb' or
                            tag == 'fanart'):
                            continue
                        structure[tag]   = elem.text
                self.__structure.append(structure)
        except IOError, e:
            raise ProgramError("Cannot access file: %s" % (e))
        except xml.parsers.expat.ExpatError, e:
            raise ProgramError("File structure invalid in '%s': %s" % (file, e))

    def __len__(self):
        return len(self.__structure)

    def get_type(self):
        '''Returns the nfo file type (to identify the kind of movie)'''
        return self.__nfotype

    def get_value(self, tag, default='', before='', after='', index=0):
        '''Returns a field value as a text'''
        Result  = default
        if self.__structure[index].has_key(tag) and self.__structure[index][tag] != None:
            Result = self.__structure[index][tag]
        if Result != '':
            Result  = before + Result + after
        return GetUnicodeString(Result)

    def get_number(self, tag, default=0, index=0):
        '''Returns a field value as a number.
        If it is not convertible to a number, the default value is returned
        '''
        Result  = default
        try:
            if tag in self.__structure[index]:
                Result = int(self.__structure[index][tag])
        except Exception:
            pass;
        return Result

    def as_text(self, index=0):
        '''Returns a text showing everything read from nfo file'''
        Result  = u'Type : '+self.__nfotype+"\n"
        for key in sorted(self.__structure[index].keys()):
            Result  = Result + key + ' : ' + self.__structure[index][key] + "\n"
        return Result


class NfoFileList:
    '''Stores all the .nfo files found.
    Store the base name (without the .nfo extension) and all the extensions
    of the associated files
    '''
    def __init__(self):
        self.__Content   = {}
        # Compiled from various sources
        self.__sidecars  = [
                        '-fanart',
                        '-thumb',
                        '-poster',
                        '-banner',
                        '-cdart',
                        '-cd-art',
                        '-clearart',
                        '-landscape',
                        '-trailer',
                    ]
        self.__reoneext  = re.compile('^\.[^.]+$')
        self.__relangext = re.compile('^\.[\w]+\.[^.]+$', re.LOCALE | re.UNICODE)

    def __iter__(self):
        for n in sorted(self.__Content.keys()):
            yield n

    def __getitem__(self, name):
        return self.__Content[name]

    def AddFile(self, file):
        '''Adds a file to rename if it has a .nfo description
        Checks if the given file has a corresponding .nfo file and add
        all files corresponding to the .nfo file
        '''
        logging.debug("Adding file %s", file)
        Name = self.__CheckExtension(file)
        if Name != '':
            self.__AddAllFiles(Name)

    def List(self):
        '''Returns an iterable on the file list'''
        return self.__Content.iteritems()

    def __CheckExtension(self, file):
        '''Find nfo file
        Find the .nfo file corresponding to the filename given.
        If nothing is found it returns an empty string'''
        (FileNoExt, FileExt)   = os.path.splitext(os.path.normpath(file))
        # No extension, it cannot be correct
        if FileExt == '':
            return ''
        # The simpler: already a .nfo file
        if FileExt == '.nfo':
            return FileNoExt
        # Is it a fanart file ?
        for n in self.__sidecars:
            if FileNoExt[-len(n):] == n:
                FileNoExt   = FileNoExt[:len(FileNoExt)-len(n)]
                break
        # Is the file without extension as a .nfo file corresponding ?
        if os.path.exists(FileNoExt + '.nfo'):
            return FileNoExt
        # Check for subtitle files in the form .language.extension
        (FileNoExt2, FileExt2)    = os.path.splitext(os.path.normpath(FileNoExt))
        if (FileExt2 != '') and os.path.exists(FileNoExt2 + '.nfo'):
            return FileNoExt2
        # Nothing detected
        return ''

    def __AddAllFiles(self, root):
        '''Add all files corresponding to a .nfo root name'''
        # Already done
        if root in self.__Content:
            return
        # Don't add special .nfo
        if os.path.basename(root) == 'tvshow':
            return
        logging.debug("Adding %s", root)
        self.__Content[root]    = []
        # Did it with glob but did not find a way to quote special characters
        # not some unicode characters, so it is a bit dumb and long but it works
        RootDir = os.path.dirname(root)
        for name in os.listdir(os.path.dirname(root)):
            fullname    = os.path.join(RootDir, name)
            if fullname[:len(root)] != root:
                continue;
            EndName = fullname[len(root):]
            if (re.search(self.__reoneext, EndName) or
                re.search(self.__relangext, EndName)):
                logging.debug("      Adding ext '%s'", EndName)
                self.__Content[root].append(EndName)
            for n in self.__sidecars:
                if EndName[:len(n)+1] == n+'.':
                    logging.debug("      Adding ext '%s'", EndName)
                    self.__Content[root].append(EndName)


def GenerateName(Format, Fields):
    '''Generates the new name of the file
    Acccording to the wanted format and the field value, it generates a new
    file name (without the extension) suitable with the movie or the serie
    episode'''

    SpecialChars    = {
            '\\'   : '\\',
            'a'    : '\a',
            'b'    : '\b',
            'e'    : '\e',
            'f'    : '\f',
            'n'    : '\n',
            'r'    : '\r',
            't'    : '\t'}
    def FieldReplacement (match):
        if match.group(1) == '%':
            return '%'
        Key = match.group(3).lower()
        Length  = match.group(2)
        if not Fields.has_key(Key):
            return ''
        if isinstance(Fields[Key], str) or isinstance(Fields[Key], unicode):
            Letter  = 's'
        else:
            Letter  = 'd'
        Fmt = '%(' + Key + ')'
        if Length != None:
            Fmt = Fmt + match.group(2)
        Fmt = Fmt + Letter
        return Fmt % Fields

    def SpecialReplacement (match):
        Letter = match.group(1)
        if not SpecialChars.has_key(Letter):
            return '\\'+Letter
        return SpecialChars[Letter];

    Result  = re.sub(r'\\([a-z])', SpecialReplacement, Format)
    Result  = re.sub('%(%|(-?\d+)?\{([a-z]+)\})',
                        FieldReplacement,
                        Result)
    return Result


def GetSubNumbers(nfodef, field, length=1, before='', end=''):
    result=u''
    for n in range(1, len(nfodef)):
        number = nfodef.get_number(field, index=n)
        result += u"%s%0*d%s" % (before, length, number, end)
    return result


def DoRename(Options, Name, Exts):
    '''Do the job of renaming a set of files associated with a .nfo file
    Reads the .nfo file to find the kind of file: movie or episode.
    Then extract all usable fields and rename all the files associated with
    the .nfo'''

    DirName = os.path.dirname(Name)
    NfoFile = Name + '.nfo'
    NfoDef  = NfoContent(NfoFile);
    NewName = "nada"
    Fields  = {}
    if (NfoDef.get_type() == 'movie'):
        if Options.movieserieonly == 1:
            return
        Title       = NfoDef.get_value('title', default=os.path.basename(Name))
        OrigTitle   = ''
        if Title != NfoDef.get_value('originaltitle'):
            OrigTitle   = NfoDef.get_value('originaltitle',
                                           before=Options.origtitlestart,
                                           after=Options.origtitleend)
        Fields   = {
            'title'     : Title,
            'origtitle' : OrigTitle,
            'year'      : NfoDef.get_value('year',
                                           before=Options.yearstart,
                                           after=Options.yearend),
            'director'  : NfoDef.get_value('director',
                                           before=Options.directorstart,
                                           after=Options.directorend),
            'credit'    : NfoDef.get_value('credit',
                                           before=Options.creditstart,
                                           after=Options.creditend),
            'runtime'   : NfoDef.get_value('runtime'),
            'studio'    : NfoDef.get_value('studio',
                                           before=Options.studiostart,
                                           after=Options.studioend),
            'set'       : NfoDef.get_value('set',
                                           before=Options.setstart,
                                           after=Options.setend),
            'sorttitle' : NfoDef.get_value('sorttitle',
                                           before=Options.sorttitlestart,
                                           after=Options.sorttitleend),
            'country'   : NfoDef.get_value('country',
                                           before=Options.countrystart,
                                           after=Options.countryend),
            'genre'     : NfoDef.get_value('genre',
                                           before=Options.genrestart,
                                           after=Options.genreend),
            }
        if Options.forcesort:
            if Fields['set'] == '':
                Fields['set']   = Options.setstart + Title + Options.setend
            if Fields['sorttitle'] == '':
                Fields['sorttitle']   = Options.sorttitlestart + Title + Options.sorttitleend

        Format  = Options.movieformat

    elif (NfoDef.get_type() == 'episodedetails'):
        if Options.movieserieonly == 2:
            return
        TvShowNfoName   = os.path.join(DirName, Options.serienfo)
        if os.path.exists(TvShowNfoName):
            TvShowNfoDef    = NfoContent(TvShowNfoName)
            if TvShowNfoDef.get_type() != 'tvshow':
                raise ProgramError("File %s is not a tvshow nfo file" % (TvshowNfoDef))
            SerieName   = TvShowNfoDef.get_value('title')
            SerieGenre  = TvShowNfoDef.get_value('genre',
                                                 before=Options.genrestart,
                                                 after=Options.genreend)
        else:
            SerieName   = NfoDef.get_value('showtitle')
            SerieGenre  = ''

        Fields   = {
            'serie'     : SerieName,
            'season'    : NfoDef.get_number('season'),
            'episode'   : NfoDef.get_number('episode'),
            'episodegrouped' : GetSubNumbers(NfoDef,
                                             'episode',
                                             length=Options.episodegroupedlength,
                                             before=Options.episodegroupedstart,
                                             end=Options.episodegroupedend),
            'displayseason': NfoDef.get_number('displayseason'),
            'displayepisode': NfoDef.get_number('displayepisode'),
            'displayepisodegrouped' : GetSubNumbers(NfoDef,
                                             'displayepisode',
                                             length=Options.episodegroupedlength,
                                             before=Options.episodegroupedstart,
                                             end=Options.episodegroupedend),
            'title'     : NfoDef.get_value('title', default=os.path.basename(Name)),
            'origtitle' : '',
            'year'      : '',
            'director'  : NfoDef.get_value('director',
                                           before=Options.directorstart,
                                           after=Options.directorend),
            'credit'    : NfoDef.get_value('credit',
                                           before=Options.creditstart,
                                           after=Options.creditend),
            'aired'     : NfoDef.get_value('aired',
                                           before=Options.yearstart,
                                           after=Options.yearend),
            'studio'    : NfoDef.get_value('studio',
                                           before=Options.studiostart,
                                           after=Options.studioend),
            'genre'     : SerieGenre,
            }
        Format  = Options.episodeformat
    else:
        logging.debug("Fichier %s de type inconnu", NfoFile)
        return


    NewName = GenerateName(Format, Fields)
    # If list is asked, just print new name
    if Options.list:
        print NewName
    else:
        NewName = re.sub("\s", Options.spacereplacement, NewName)
        if Options.invalidcharacters != "":
            NewName = re.sub("[" + re.escape(Options.invalidcharacters) + "\000-\037\177]+",
                             Options.invalidcharacterreplacement,
                             NewName)


        # Special case for DVD structure
        if os.path.basename(NfoFile) == 'VIDEO_TS.nfo':
            # Another check to be sure
            if Options.skipdvd or os.path.basename(DirName) != 'VIDEO_TS':
                return
            OldPath         = os.path.dirname(DirName)
            ActualDir       = os.path.dirname(OldPath)
            NewPath         = os.path.join(ActualDir, NewName)
            if OldPath != NewPath:
                print "Renaming dvd structure '%s' as '%s'" % (OldPath, NewPath)
                if os.path.exists(NewPath):
                    raise ProgramError("File/Dir '%s' already exists", NewPath)
                if not Options.debug:
                    os.rename(OldPath, NewPath)
        else:
            for ext in Exts:
                OldPath = Name + ext
                NewPath = os.path.join(DirName, NewName) + ext
                # Test if nothing to do
                if OldPath == NewPath:
                    continue
                print "Renaming '%s' as '%s'" % (OldPath, NewPath)
                if os.path.exists(NewPath):
                    raise ProgramError("File '%s' already exists", NewPath)
                if not Options.debug:
                    os.rename(OldPath, NewPath)

def HelpOnFormat():
    '''prints an help about formatting the file names'''
    print """
The format (titles of movies or episodes) are used to specify how to
rename the files.
They are composed of static text and fields. Everything which is not a field
is static text.

A field begins with the percent character '%' followed by an optional length
and a field name enclosed between curly brackets: %{title}
The length is a minimal length, if the field is longer than the specified
length it is the field length which is taken.
If the length is positive, it is right justified. If the length is negative
it is left justified.
For a number (seasons and episode numbers), if the length begins with '0',
the padding character is '0' instead of ' ': %03{episode} gives '002' for
episode #2.
If a field in a format is unknown, it is expanded as an empty string.
If you want to insert a simple percent (not a field) just double it: '%%'

The known fields for movies are:
    title    : the movie title (current language)
    origtitle: the original title
    year     : the year the movie was first projected (considered as text,
                not a number)
    director : name of the director
    credit   : credits (as in the .fo file)
    runtime  : the movie duration (text too)
    studio   : the studio where the movie was made
    set      : the set of movies the current movie belongs to (for example,
               the set 'Superman' is for all the movies concerning Superman)
    sorttitle: the string used to sort the movie by name (to have them in the
               right order in a set, for example)
    country  : the country where the movie was made
    genre    : the category of movie

The known fields for serie episodes are:
    serie         : the serie name (extracted from tvshow.nfo or current
                    nfo if tvshow is missing)
    season        : the season number (as a number)
    episode       : the episode number (as a number)
    episodegrouped: the other episodes making the video (for example
                    episodes 1 & 2 in only one video; as a string)
    displayseason : the display season number (as a number)
    displayepisode: the display episode number (as a number)
    displayepisodegrouped: the other display episodes making the video
                    (for example episodes 1 & 2 in only one video; as a
                    string)
    title         : the title of the episode (in the current language)
    origtitle     : the original title of the episode (if available)
    year          : always empty.
    director      : the director of the episode
    credit        : the credits for the episode
    aired         : the first time the episode was aired
    studio        : the studio where the episode was made
    genre         : the category of the serie (identical for all episodes,
                    extracted from the file tvshow.nfo)

Exemple of --list option usage in order to make a LibreOffice Calc
textfile with fields separated by tabs (put everything on two lines,
one per command):
printf "type\\tset/serie\\tSort title/Episode number\\tTitle\\tOriginal title\\tYear/Aired\\tDirector\\tGenre\\n"
  >mylist.xls
XBMCRename.py
  --list
  --movieformat="movie\\t%{set}\\t%{sorttitle}\\t%{title}\\t%{origtitle}\\t%{year}\\t%{director}\\t%{genre}"
  --episodeformat="serie\\t%{serie}\\t%{season}x%03{episode}\\t%{title}\\t%{origtitle}\\t%{aired}\\t%{director}\\t%{genre}"
  --forcesort --nostart --noend
  VideoDirectory
  >>mylist.xls
    """

class StoreInvalidCharacters(argparse.Action):
    '''Callback for argparse.
    Sets the invalid characters according to the wanted OS
    '''
    def __call__(self, parser, namespace, values, option_string=None):
        setattr(namespace, self.dest, values)
        if option_string == '--unixfilesystem':
            setattr(namespace, self.dest, '/')
        elif option_string == '--windowsfilesystem':
            setattr(namespace, self.dest, ':\\<>|*')
        elif option_string == '--macosfilesystem':
            setattr(namespace, self.dest, ":")
        elif option_string == '--xboxfilesystem':
            setattr(namespace, self.dest, '<>=?:;\\*+,/|()')
        elif option_string == '--allfilesystems':
            setattr(namespace, self.dest, '!"#$%&\'()*+,/;<=>?@[\]^`{|}')
        elif option_string == '--myfilesystem':
            setattr(namespace, self.dest, values[0])
        else:
            raise ProgramError("PROGRAM ERROR: Option '%s' unknown", option_string)

class SetNoStartEnd(argparse.Action):
    def __call__(self, parser, namespace, values, option_string=None):
        if (option_string[-5:] == 'start'):
            marker  = 'start'
        else:
            marker  = 'end'
        for name in ('origtitle',
                     'year',
                     'director',
                     'credit',
                     'studios',
                     'set',
                     'sorttitle',
                     'country',
                     'genre',
                     'episodegrouped'):
            setattr(name, name + marker, "")

class SetSerieMovieOnly(argparse.Action):
    def __call__(self, parser, namespace, values, option_string=None):
        print namespace, values, option_string
        setattr(namespace, self.dest, values)
        if (option_string == '--movieonly'):
            setattr(namespace, self.dest, 2)
        else:
            setattr(namespace, self.dest, 1)


def main():
    '''Interprets the command line and do the job'''
    global Version
    usage = "%(prog)s [options]  {file|dir}...\n  %(prog)s --help"
    description = "Renames files in XBMC (or similar programs) according to " \
                  "the content of .nfo files. File with the same beginning " \
                    "as the .nfo file are renamed the same way (i.e. video " \
                    "files, fanart, icons, subtitles...). "                 \
                    "The format used for renaming is choosen after reading " \
                    "the .nfo file and determining what kind or video is "   \
                    "involved (movie or serie episode). "                  \
                    "'file' can be a .nfo file or any other file, the corresponding " \
                    ".nfo file is searched in the same directory. If not found, the " \
                    "file is ignored. If a directory ('dir') is used all files in "   \
                    "this directory are treated."
    parser = argparse.ArgumentParser(
                          description = description)


    parser.add_argument("-m", "--movieformat",
                        default="%{title}%{origtitle}%{year}",
                        help="defines movie file name format"
                              " [default=\"%(default)s\"].")
    parser.add_argument("-e", "--episodeformat",
                        default="%{serie} - [%{season}x%03{episode}%{episodegrouped}] - %{title}%{origtitle}",
                        help="defines serie episode file name format"
                              " [default=\"%(default)s\"].")
    parser.add_argument("--episodegroupedlength",
                        type=int,
                        default=3,
                        help="defines the number of digits to be used for grouped episodes "
                             "(i.e. the episodes following the main episodes in the same video)"
                             " [default=%(default)s].")
    parser.add_argument("-f", "--formathelp",
                        action="store_true",
                        help="shows help for movie/episode name formats.")
    parser.add_argument("--forcesort",
                        action="store_true",
                        help="force set and sort title to title if they are not defined.")
    parser.add_argument("--movieonly",
                        action=SetSerieMovieOnly,
                        nargs=0,
                        dest='movieserieonly',
                        help="Keep only movies (ignore episodes).")
    parser.add_argument("--serieonly",
                        action=SetSerieMovieOnly,
                        nargs=0,
                        dest='movieserieonly',
                        help="Keep only episodes from series (ignore movies).")
    parser.add_argument("-l", "--list",
                        action="store_true",
                        help="List destination names names instead or renaming them.")
    parser.add_argument("-d", "--debug",
                        action="store_true",
                        help="shows lot of nasty debug informations.")
    parser.add_argument("--version",
                        action='version',
                        version=Version)
    parser.add_argument("files",
                        nargs='+',
                        metavar='file|dir',
                        help="File or directory to scan")


    group   = parser.add_argument_group(
                            "Separators",
                            "Separators used before and after some fields if they "
                              "are not empty. It avoids something like \"   [] \" if "
                              "there is 2 or three fields empty")
    group.add_argument("--origtitlestart",
                       default=" [",
                       help="begining for original title"
                            " [default=\"%(default)s\"].")
    group.add_argument("--origtitleend",
                       default="]",
                       help="ending for original title"
                            " [default=\"%(default)s\"].")
    group.add_argument("--yearstart",
                       default=" (",
                       help="beginning for year"
                            " [default=\"%(default)s\"].")
    group.add_argument("--yearend",
                       default=")",
                       help="ending for year"
                            " [default=\"%(default)s\"].")
    group.add_argument("--directorstart",
                       default=", ",
                       help="beginning for director"
                            " [default=\"%(default)s\"].")
    group.add_argument("--directorend",
                       default="",
                       help="ending for director"
                            " [default=\"%(default)s\"].")
    group.add_argument("--creditstart",
                       default=", ",
                       help="beginning for credit"
                            " [default=\"%(default)s\"].")
    group.add_argument("--creditend",
                       default="",
                       help="ending for credit"
                            " [default=\"%(default)s\"].")
    group.add_argument("--studiostart",
                       default=", ",
                       help="beginning for studio"
                            " [default=\"%(default)s\"].")
    group.add_argument("--studioend",
                       default="",
                       help="ending for studio"
                            " [default=\"%(default)s\"].")
    group.add_argument("--setstart",
                       default=" - ",
                       help="beginning for set names"
                             " [default=\"%(default)s\"].")
    group.add_argument("--setend",
                       default=" -",
                       help="ending for set names"
                             " [default=\"%(default)s\"].")
    group.add_argument("--sorttitlestart",
                       default=" ",
                       help="beginning for sort title"
                            " [default=\"%(default)s\"].")
    group.add_argument("--sorttitleend",
                       default="",
                       help="ending for sorttitle"
                            " [default=\"%(default)s\"].")
    group.add_argument("--countrystart",
                       default=" ",
                       help="beginning for country"
                            " [default=\"%(default)s\"].")
    group.add_argument("--countryend",
                       default="",
                       help="ending for country"
                            " [default=\"%(default)s\"].")
    group.add_argument("--genrestart",
                       default=" ",
                       help="beginning for genre"
                            " [default=\"%(default)s\"].")
    group.add_argument("--genreend",
                       default="",
                       help="ending for genre"
                            " [default=\"%(default)s\"].")
    group.add_argument("--episodegroupedstart",
                       default="-",
                       help="beginning for grouped episode"
                            " [default=\"%(default)s\"].")
    group.add_argument("--episodegroupedend",
                       default="",
                       help="ending for grouped episode"
                            " [default=\"%(default)s\"].")
    group.add_argument("--nostart",
                       action=SetNoStartEnd,
                       nargs=0,
                       help="Set all values for --startxxx options to empty string. "
                            "Then you can add new values. TIP: can be used with --list option")
    group.add_argument("--noend",
                       action=SetNoStartEnd,
                       nargs=0,
                       help="Set all values for --endxxx options to empty string. "
                            "Then you can add new values. TIP: can be used with --list option")


    group   = parser.add_argument_group(
                            "Special characters",
                            "Some characters are illegal with some file system "
                             "and these sets are different according to the OS. "
                             "These options allow to replace offending characters "
                             "by others which are accepted (ignored if --list "
                             "is used)")
    group.add_argument("--spacereplacement",
                       default=" ",
                       help="string used to replace spaces (can be empty). "
                            "Spaces are accepted merely everywhere but are often "
                            "difficult to manage on a command line"
                            " [default=\"%(default)s\"].")
    group.add_argument("--classicfilesystem",
                       dest="invalidcharacters",
                       default='/\\:<>*|',
                       help="string representing the characters to replace to "
                            "be portable on most filesystems "
                            "(unix/windows)"
                            " [default=\"%(default)s\"].")
    group.add_argument("--allfilesystems",
                       dest="invalidcharacters",
                       action=StoreInvalidCharacters,
                       nargs=0,
                       help="string representing the characters to replace to "
                            "be portable on merely all filesystems "
                            "(unix/windows/macOS/fat32/vfat).")
    group.add_argument("--unixfilesystem",
                       dest="invalidcharacters",
                       action=StoreInvalidCharacters,
                       nargs=0,
                       help="string representing the characters to replace to "
                            "be able to write any title on unix filesystems.")
    group.add_argument("--windowsfilesystem",
                       dest="invalidcharacters",
                       action=StoreInvalidCharacters,
                       nargs=0,
                       help="string representing the characters to replace to "
                            "be able to write any title on windows filesystems.")
    group.add_argument("--macosfilesystem",
                       dest="invalidcharacters",
                       action=StoreInvalidCharacters,
                       nargs=0,
                       help="string representing the characters to replace to "
                            "be able to write any title on macos filesystems.")
    group.add_argument("--xboxfilesystem",
                       dest="invalidcharacters",
                       action=StoreInvalidCharacters,
                       nargs=0,
                       help="string representing the characters to replace to "
                            "be able to write any title on macos filesystems.")
    group.add_argument("--myfilesystem",
                       dest="invalidcharacters",
                       action=StoreInvalidCharacters,
                       nargs=1,
                       help="string representing the characters you personally "
                            "choose to replace.")
    group.add_argument("--invalidcharacterreplacement",
                       dest="invalidcharacterreplacement",
                       default="-",
                       help="string by which invalid characters are replaced "
                            "(can be empty)"
                            " [default=\"%(default)s\"].")

    group   = parser.add_argument_group(
                            "File management",
                            "These options specify how to deal with special files "
                                "or directories.")
    group.add_argument("--serienfo",
                       dest="serienfo", default="tvshow.nfo",
                       help="file name where to search the serie name "
                            "(in the same directory as the episode nfo file)"
                            " [default=\"%(default)s\"].")
    group.add_argument("--skipdvd",
                       dest="skipdvd", action="store_true",
                       help="if used, skips nfo files in DVD structures, "
                            "i.e. a .nfo file names VIDEO_TS.nfo in a VIDEO_TS directory. "
                            "If not skipped, it is the directory containing the VIDEO_TS "
                            "directory which is renamed"
                            " [default=don't skip].")
    group.add_argument("--recurse", action="store_true", dest="recurse",
                       default=True,
                       help="Recurse in subdirectories if a directory is found "
                            "in a given directory"
                            " [default]")
    group.add_argument("--norecurse", action="store_false", dest="recurse",
                       default=True,
                       help="Don't recurse in subdirectories if a directory is found "
                            "in a given directory")

    options = parser.parse_args()

    if options.formathelp:
        HelpOnFormat()
        return
    if options.debug:
        logging.basicConfig(level=logging.DEBUG)
    filelist    = NfoFileList()
    index       = 0
    args = options.files
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
    for name in filelist:
        DoRename(options, name, filelist[name])

if __name__ == "__main__":
    try:
        main()
    except ProgramError, e:
        print "Error: "+str(e)

