/* ------------------------------------------------------------------------
@NAME       : my_dmalloc.h
@DESCRIPTION: Tiny header file to possibly include <dmalloc.h> (ie. the
              "real thing"), depending on the DMALLOC preprocessor token.
@CREATED    : 1997/09/06, Greg Ward
@MODIFIED   : 
@VERSION    : $Id: my_dmalloc.h 221 1997-09-07 02:16:52Z greg $
-------------------------------------------------------------------------- */

#ifndef MY_DMALLOC_H
#define MY_DMALLOC_H

#ifdef DMALLOC
# include <dmalloc.h>
#endif

#endif /* MY_DMALLOC_H */
