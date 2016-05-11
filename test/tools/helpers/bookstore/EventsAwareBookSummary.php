<?php
class EventsAwareBookSummary extends BaseEventsAwareBookSummary
{
    /**
     * Method called to associate a BookOfficialSummaryEvent object to this object
     * through the BookOfficialSummaryEvent foreign key attribute.
     *
     * Book of summary is set to the book provided by the event.
     *
     * @param  BookOfficialSummaryEvent $e BookOfficialSummaryEvent
     * @return EventsAwareBookSummary The current object (for fluent API support)
     */
    public function addBookOfficialSummaryEvent(BookOfficialSummaryEvent $e)
    {
        // determine whether the event is already attached to this book
        $isNew  = !$this->collBookOfficialSummaryEvents
            || !in_array($e, $this->collBookOfficialSummaryEvents->getArrayCopy(), true);

        parent::addBookOfficialSummaryEvent($e);

        $book = $e->getEventsAwareBook();

        // if the event is new, set official summary accordingly
        if ($isNew) {
            $this->setSummarizedBook($book);
        }

        // if the book does not have a offical summary, assign it
        if ($book && $book->getOfficialSummaryId() === null && $book->getOfficialSummary(null, false) === null) {
            $book->setOfficialSummary($this);
        }

        return $this;
    }
}
