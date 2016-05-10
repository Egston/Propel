<?php
class EventsAwareBook extends BaseEventsAwareBook
{
    /**
     * Method called to associate a BookOfficialSummaryEvent object to this object
     * through the BookOfficialSummaryEvent foreign key attribute.
     *
     * Book official summary is set to the new official summary provided by the event.
     *
     * @param    BookOfficialSummaryEvent $l BookOfficialSummaryEvent
     * @return Book The current object (for fluent API support)
     */
    public function addBookOfficialSummaryEvent(BookOfficialSummaryEvent $e)
    {
        // determine whether the event is already attached to this book
        $isNew  = !$this->getBookOfficialSummaryEvents()->contains($e, true);

        parent::addBookOfficialSummaryEvent($e);

        $summary = $e->getNewOfficialSummary();

        // if the event is new, set official summary accordingly
        if ($isNew) {
            $this->setOfficialSummary($summary);
        }

        // if the summary does not have a book, assign it
        if ($summary && $summary->getBookId() === null && $summary->getSummarizedBook(null, false) === null) {
            $summary->setSummarizedBook($this);
        }

        return $this;
    }
}
